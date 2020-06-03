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

namespace Sylius\Bundle\PayumBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RegisterGatewayConfigTypePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('sylius.form_registry.payum_gateway_config')) {
            return;
        }

        $formRegistry = $container->findDefinition('sylius.form_registry.payum_gateway_config');
        $gatewayFactories = [['priority' => 0, 'label' => 'sylius.payum_gateway_factory.offline', 'type' => 'offline']];

        $gatewayConfigurationTypes = $container->findTaggedServiceIds('sylius.gateway_configuration_type');

        foreach ($gatewayConfigurationTypes as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['type'], $attribute['label'])) {
                    throw new \InvalidArgumentException('Tagged gateway configuration type needs to have `type` and `label` attributes.');
                }

                $gatewayFactories[] = [
                    'label' => $attribute['label'],
                    'priority' => $attribute['priority'] ?? 0,
                    'type' => $attribute['type'],
                ];

                $formRegistry->addMethodCall(
                    'add',
                    ['gateway_config', $attribute['type'], $container->getDefinition($id)->getClass()]
                );
            }
        }


        usort($gatewayFactories, function (array $firstGateway, array $secondGateway): int {
            return $secondGateway['priority'] - $firstGateway['priority'];
        });

        foreach ($gatewayFactories as $key => $factory) {
            $gatewayFactories[$factory['type']] = $factory['label'];
            unset($gatewayFactories[$key]);
        }

        $container->setParameter('sylius.gateway_factories', $gatewayFactories);
    }
}
