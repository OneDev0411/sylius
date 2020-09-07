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

namespace Sylius\Bundle\ApiBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\ApiBundle\DependencyInjection\Compiler\CommandDataTransformerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class CommandDataTransformerPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_collects_tagged_command_data_transformer_services(): void
    {
        $this->setDefinition(
            'sylius.api.command_data_transformer.service.first',
            (new Definition())->addTag('sylius.api.command_data_transformer')
        );

        $this->setDefinition(
            'sylius.api.command_data_transformer.service.second',
            (new Definition())->addTag('sylius.api.command_data_transformer')
        );

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'sylius.api.command_data_transformer.service.first',
            'sylius.api.command_data_transformer'
        );

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'sylius.api.command_data_transformer.service.second',
            'sylius.api.command_data_transformer'
        );
    }

    /**
     * @test
     */
    public function it_adds_command_data_transformer_chain_services(): void
    {
        $this->compile();

        $this->assertContainerBuilderHasService('sylius_api.command_data_transformers_chain');
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CommandDataTransformerPass());
    }
}
