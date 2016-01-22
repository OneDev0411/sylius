<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Tests\Loader;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Sylius\Bundle\ThemeBundle\Loader\ThemeConfiguration;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function it_requires_only_name()
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['name' => 'example/sylius-theme'],
            ],
            ['name' => 'example/sylius-theme']
        );
    }

    /**
     * @test
     */
    public function its_name_is_required_and_cannot_be_empty()
    {
        $this->assertPartialConfigurationIsInvalid(
            [
                [/* no name defined */],
            ],
            'name'
        );

        $this->assertPartialConfigurationIsInvalid(
            [
                ['name' => ''],
            ],
            'name'
        );
    }

    /**
     * @test
     */
    public function its_title_is_optional_but_cannot_be_empty()
    {
        $this->assertPartialConfigurationIsInvalid(
            [
                ['title' => ''],
            ],
            'title'
        );

        $this->assertConfigurationIsValid(
            [
                ['title' => 'Lorem ipsum'],
            ],
            'title'
        );
    }

    /**
     * @test
     */
    public function its_description_is_optional_but_cannot_be_empty()
    {
        $this->assertPartialConfigurationIsInvalid(
            [
                ['description' => ''],
            ],
            'description'
        );

        $this->assertConfigurationIsValid(
            [
                ['description' => 'Lorem ipsum dolor sit amet'],
            ],
            'description'
        );
    }

    /**
     * @test
     */
    public function its_path_is_optional_but_cannot_be_empty()
    {
        $this->assertPartialConfigurationIsInvalid(
            [
                ['path' => ''],
            ],
            'path'
        );

        $this->assertConfigurationIsValid(
            [
                ['path' => '/theme/path'],
            ],
            'path'
        );
    }

    /**
     * @test
     */
    public function its_authors_are_optional()
    {
        $this->assertConfigurationIsValid(
            [
                [/* no authors defined */],
            ],
            'authors'
        );
    }

    /**
     * @test
     */
    public function its_author_can_have_only_name_email_homepage_and_role_properties()
    {
        $this->assertConfigurationIsValid(
            [
                ['authors' => [['name' => 'Kamil Kokot']]],
            ],
            'authors'
        );

        $this->assertConfigurationIsValid(
            [
                ['authors' => [['email' => 'kamil@kokot.me']]],
            ],
            'authors'
        );

        $this->assertConfigurationIsValid(
            [
                ['authors' => [['homepage' => 'http://kamil.kokot.me']]],
            ],
            'authors'
        );

        $this->assertConfigurationIsValid(
            [
                ['authors' => [['role' => 'Developer']]],
            ],
            'authors'
        );

        $this->assertPartialConfigurationIsInvalid(
            [
                ['authors' => [['undefined' => '42']]],
            ],
            'authors'
        );
    }

    /**
     * @test
     */
    public function its_author_must_have_at_least_one_property()
    {
        $this->assertPartialConfigurationIsInvalid(
            [
                ['authors' => [[/* empty author */]]],
            ],
            'authors',
            'Author cannot be empty'
        );
    }

    /**
     * @test
     */
    public function its_authors_replaces_other_authors_defined_elsewhere()
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['authors' => [['name' => 'Kamil Kokot']]],
                ['authors' => [['name' => 'Krzysztof Krawczyk']]],
            ],
            ['authors' => [['name' => 'Krzysztof Krawczyk']]],
            'authors'
        );
    }

    /**
     * @test
     */
    public function it_ignores_undefined_root_level_fields()
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['name' => 'example/sylius-theme', 'undefined_variable' => '42'],
            ],
            ['name' => 'example/sylius-theme']
        );
    }

    /**
     * @test
     */
    public function its_parents_are_optional_but_has_to_have_at_least_one_element()
    {
        $this->assertConfigurationIsValid(
            [
                [],
            ],
            'parents'
        );

        $this->assertPartialConfigurationIsInvalid(
            [
                ['parents' => [/* no elements */]],
            ],
            'parents'
        );
    }

    /**
     * @test
     */
    public function its_parent_is_strings()
    {
        $this->assertConfigurationIsValid(
            [
                ['parents' => ['example/parent-theme', 'exampe/parent-theme-2']],
            ],
            'parents'
        );
    }

    /**
     * @test
     */
    public function its_parent_cannot_be_empty()
    {
        $this->assertPartialConfigurationIsInvalid(
            [
                ['parents' => ['']],
            ],
            'parents'
        );
    }

    /**
     * @test
     */
    public function its_parents_replaces_other_parents_defined_elsewhere()
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['parents' => ['example/first-theme']],
                ['parents' => ['example/second-theme']],
            ],
            ['parents' => ['example/second-theme']],
            'parents'
        );
    }

    protected function getConfiguration()
    {
        return new ThemeConfiguration();
    }
}
