<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReviewBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class SyliusReviewExtension extends AbstractResourceExtension
{
    /**
     * @var array
     */
    private $reviewSubjects = array();

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->configure(
            $config,
            new Configuration(),
            $container,
            self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS | self::CONFIGURE_FORMS
        );

        foreach ($config['classes'] as $name => $parameters) {
            $this->addRequiredArgumentsToForms($name, $parameters, $container);
        }
        foreach ($this->reviewSubjects as $subject) {
            $this->addProperTagToReviewDeleteListener($subject, $container);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function process(array $config, ContainerBuilder $container)
    {
        $subjects = array();
        $convertedConfig = array();

        foreach ($config['classes'] as $subject => $parameters) {
            $this->reviewSubjects[] = $subject;

            $subjects[$subject] = $parameters;
            unset($parameters['subject']);

            $convertedConfig = array_merge($convertedConfig, $this->formatClassesConfiguration($parameters, $subject));
            $config['validation_groups'] = $this->modifyValidationGroups($config['validation_groups'], $subject);
        }
        $container->setParameter('sylius.review.subjects', $subjects);
        $config['classes'] = $convertedConfig;

        $convertedConfig = array();
        foreach ($config['validation_groups'] as $subject => $parameters) {
            $convertedConfig = array_merge($convertedConfig, $this->formatValidationGroups($parameters, $subject));
        }

        $config['validation_groups'] = $convertedConfig;

        return parent::process($config, $container);
    }

    /**
     * @param string           $name
     * @param array            $parameters
     * @param ContainerBuilder $container
     */
    private function addRequiredArgumentsToForms($name, array $parameters, ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.form.type.'.$name)) {
            return;
        }

        foreach ($parameters['form'] as $formName => $form) {
            $formKey = ('default' === $formName) ? $name : $name.'_'.$formName;

            $container->getDefinition('sylius.form.type.'.$formKey)->addArgument($parameters['subject']);
        }
    }

    /**
     * @param string           $resourceName
     * @param ContainerBuilder $container
     */
    private function addProperTagToReviewDeleteListener($resourceName, ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.listener.review_delete')) {
            return;
        }

        $listenerDefinition = $container->getDefinition('sylius.listener.review_delete');
        $listenerDefinition->addTag('kernel.event_listener', array('event' => 'sylius.'.$resourceName.'_review.post_delete', 'method' => 'recalculateSubjectRating'));
    }

    /**
     * @param array  $parameters
     * @param string $subject
     *
     * @return array
     */
    private function formatClassesConfiguration(array $parameters, $subject)
    {
        $convertedConfig = array();
        foreach ($parameters as $resource => $classes) {
            $convertedConfig[$subject.'_'.$resource] = $classes;
            $convertedConfig[$subject.'_'.$resource]['subject'] = $subject;
        }

        return $convertedConfig;
    }

    /**
     * @param array  $validationGroups
     * @param string $subject
     *
     * @return mixed
     */
    private function modifyValidationGroups(array $validationGroups, $subject)
    {
        if (!isset($validationGroups[$subject]['review'])) {
            $validationGroups[$subject]['review'] = array('sylius', 'sylius_review');
        }
        if (!isset($validationGroups[$subject]['review_admin'])) {
            $validationGroups[$subject]['review_admin'] = array('sylius');
        }

        return $validationGroups;
    }

    /**
     * @param array  $parameters
     * @param string $subject
     *
     * @return array
     */
    private function formatValidationGroups(array $parameters, $subject)
    {
        $convertedConfig = array();
        foreach ($parameters as $resource => $validationGroups) {
            if (!is_int($resource)) {
                $convertedConfig[$subject.'_'.$resource] = $validationGroups;
            }
        }

        return $convertedConfig;
    }
}
