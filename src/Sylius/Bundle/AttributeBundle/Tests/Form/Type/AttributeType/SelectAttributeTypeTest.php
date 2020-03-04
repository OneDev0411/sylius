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

namespace Sylius\Bundle\AttributeBundle\Tests\Form\Type\AttributeType;

use PHPUnit\Framework\Assert;
use Prophecy\Prophecy\ProphecyInterface;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class SelectAttributeTypeTest extends TypeTestCase
{
    /** @var ProphecyInterface|TranslationLocaleProviderInterface */
    private $translationProvider;

    public function test_it_return_all_choices()
    {
        $this->assertChoicesLabels(['', 'value 1'], [
            'configuration' => [
                'multiple' => false,
                'min' => null,
                'max' => null,
                'choices' => ['val1' => ['en_GB' => 'value 1'], 'val2' => ['fr_FR' => 'valeur 2']],
            ],
        ]);
    }

    private function assertChoicesLabels(array $expectedLabels, array $formConfiguration = []): void
    {
        $form = $this->factory->create(\Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\SelectAttributeType::class,
            null, $formConfiguration);
        $view = $form->createView();

        Assert::assertSame($expectedLabels, array_map(function (ChoiceView $choiceView): string {
            return $choiceView->label;
        }, $view->vars['choices']));
    }

    protected function setUp()
    {
        $this->translationProvider = $this->prophesize(TranslationLocaleProviderInterface::class);
        $this->translationProvider->getDefaultLocaleCode()->willReturn('en_GB');

        parent::setUp();
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtensions()
    {
        $type = new \Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\SelectAttributeType($this->translationProvider->reveal());

        return [
            new PreloadedExtension([$type], []),
        ];
    }
}
