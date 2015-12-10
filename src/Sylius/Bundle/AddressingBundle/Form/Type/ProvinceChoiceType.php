<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Form\Type;

use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class ProvinceChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = function (Options $options) {
                if (null === $options['country']) {
                    $choices = $this->repository->findAll();
                } else {
                    $choices = $options['country']->getProvinces();
                }

            return $this->getProvinceCodes($choices);
        };

        $resolver
            ->setDefaults(array(
                'choices'     => $choices,
                'country'     => null,
                'label'       => 'sylius.form.zone.types.province',
                'empty_value' => 'sylius.form.province.select',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_province_choice';
    }

    /**
     * @param ProvinceInterface[] $provinces
     *
     * @return array
     */
    protected function getProvinceCodes(array $provinces)
    {
        $provinceCodes = array();

        /* @var ProvinceInterface $province */
        foreach ($provinces as $province) {
            $provinceCodes[$province->getCode()] = $province->getName();
        }

        return $provinceCodes;
    }
}
