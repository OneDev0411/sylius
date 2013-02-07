<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Form\Type\Rule;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Order total rule configuration form type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class OrderTotalConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', 'money', array(
                'label' => 'sylius.form.rule.order_total_configuration.amount',
                'constraints' => array(
                    new NotBlank(),
                    new Type(array('type' => 'numeric')),
                )
            ))
            ->add('equal', 'checkbox', array(
                'label' => 'sylius.form.rule.order_total_configuration.equal',
                'constraints' => array(
                    new Type(array('type' => 'bool')),
                )
            ))
        ;
    }

    public function getName()
    {
        return 'sylius_promotion_rule_order_total_configuration';
    }
}
