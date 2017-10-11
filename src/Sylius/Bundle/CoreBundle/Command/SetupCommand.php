<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Command;

use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Webmozart\Assert\Assert;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class SetupCommand extends AbstractInstallCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('sylius:install:setup')
            ->setDescription('Sylius configuration setup.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command allows user to configure basic Sylius data.
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $currency = $this->get('sylius.setup.currency')->setup($input, $output, $this->getHelper('question'));
        $locale = $this->get('sylius.setup.locale')->setup($input, $output);
        $this->get('sylius.setup.channel')->setup($locale, $currency);
        $this->setupAdministratorUser($input, $output, $locale->getCode());
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $localeCode
     */
    protected function setupAdministratorUser(InputInterface $input, OutputInterface $output, string $localeCode): void
    {
        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln('Create your administrator account.');

        $userManager = $this->get('sylius.manager.admin_user');
        $userFactory = $this->get('sylius.factory.admin_user');

        try {
            $user = $this->configureNewUser($userFactory->createNew(), $input, $output);
        } catch (\InvalidArgumentException $exception) {
            return;
        }

        $user->setEnabled(true);
        $user->setLocaleCode($localeCode);

        $userManager->persist($user);
        $userManager->flush();

        $outputStyle->writeln('<info>Administrator account successfully registered.</info>');
        $outputStyle->newLine();
    }

    /**
     * @param AdminUserInterface $user
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return AdminUserInterface
     */
    private function configureNewUser(AdminUserInterface $user, InputInterface $input, OutputInterface $output): AdminUserInterface
    {
        $userRepository = $this->get('sylius.repository.admin_user');

        if ($input->getOption('no-interaction')) {
            Assert::null($userRepository->findOneByEmail('sylius@example.com'));

            $user->setEmail('sylius@example.com');
            $user->setUsername('sylius');
            $user->setPlainPassword('sylius');

            return $user;
        }

        $questionHelper = $this->getHelper('question');

        do {
            $question = $this->createEmailQuestion($output);
            $email = $questionHelper->ask($input, $output, $question);
            $exists = null !== $userRepository->findOneByEmail($email);

            if ($exists) {
                $output->writeln('<error>E-Mail is already in use!</error>');
            }
        } while ($exists);

        $user->setEmail($email);
        $user->setUsername($email);
        $user->setPlainPassword($this->getAdministratorPassword($input, $output));

        return $user;
    }

    /**
     * @param OutputInterface $output
     *
     * @return Question
     */
    private function createEmailQuestion(OutputInterface $output): Question
    {
        return (new Question('E-mail:'))
            ->setValidator(function ($value) {
                /** @var ConstraintViolationListInterface $errors */
                $errors = $this->get('validator')->validate((string) $value, [new Email(), new NotBlank()]);
                foreach ($errors as $error) {
                    throw new \DomainException($error->getMessage());
                }

                return $value;
            })
            ->setMaxAttempts(3)
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    private function getAdministratorPassword(InputInterface $input, OutputInterface $output)
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');
        $validator = $this->getPasswordQuestionValidator();

        do {
            $passwordQuestion = $this->createPasswordQuestion('Choose password:', $validator);
            $confirmPasswordQuestion = $this->createPasswordQuestion('Confirm password:', $validator);

            $password = $questionHelper->ask($input, $output, $passwordQuestion);
            $repeatedPassword = $questionHelper->ask($input, $output, $confirmPasswordQuestion);

            if ($repeatedPassword !== $password) {
                $output->writeln('<error>Passwords do not match!</error>');
            }
        } while ($repeatedPassword !== $password);

        return $password;
    }

    /**
     * @return \Closure
     */
    private function getPasswordQuestionValidator(): \Closure
    {
        return function ($value) {
            /** @var ConstraintViolationListInterface $errors */
            $errors = $this->get('validator')->validate($value, [new NotBlank()]);
            foreach ($errors as $error) {
                throw new \DomainException($error->getMessage());
            }

            return $value;
        };
    }

    /**
     * @param string $message
     * @param \Closure $validator
     *
     * @return Question
     */
    private function createPasswordQuestion(string $message, \Closure $validator): Question
    {
        return (new Question($message))
            ->setValidator($validator)
            ->setMaxAttempts(3)
            ->setHidden(true)
            ->setHiddenFallback(false)
        ;
    }
}
