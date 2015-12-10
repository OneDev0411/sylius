<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Symfony\Component\Intl\Intl;

/**
 * Default zone fixtures.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class LoadZonesData extends DataFixture
{
    protected $euCountries = array(
        'BE', 'BG', 'CZ', 'DK', 'DE', 'EE', 'IE', 'GR', 'ES',
        'FR', 'IT', 'CY', 'LV', 'LT', 'LU', 'HU', 'MT', 'NL',
        'AT', 'PL', 'PT', 'RO', 'SI', 'SK', 'FI', 'SE', 'GB',
    );

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $restOfWorldCountries = array_diff(
            array_keys(Intl::getRegionBundle()->getCountryNames($this->container->getParameter('sylius.locale'))),
            array_merge($this->euCountries, ['US'])
        );

        $manager->persist($eu = $this->createZone('EU', 'European Union', ZoneInterface::TYPE_COUNTRY, $this->euCountries));
        $manager->persist($this->createZone('USA', 'United States of America', ZoneInterface::TYPE_COUNTRY, array('US')));
        $manager->persist($this->createZone('EUSA', 'EU + USA', ZoneInterface::TYPE_ZONE, array('EU', 'USA')));
        $manager->persist($this->createZone('RoW', 'Rest of World', ZoneInterface::TYPE_COUNTRY, $restOfWorldCountries));

        $manager->flush();

        $settingsManager = $this->get('sylius.settings.manager');
        $settings = $settingsManager->loadSettings('sylius_taxation');
        $settings->set('default_tax_zone', $eu);
        $settingsManager->saveSettings('sylius_taxation', $settings);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 20;
    }

    /**
     * Create a new zone instance of given type.
     *
     * @param string $code
     * @param string $name
     * @param string $type
     * @param array  $members
     *
     * @return ZoneInterface
     */
    protected function createZone($code, $name, $type, array $members)
    {
        /* @var $zone ZoneInterface */
        $zone = $this->getZoneFactory()->createNew();
        $zone->setCode($code);
        $zone->setName($name);
        $zone->setType($type);

        foreach ($members as $memberCode) {
            /* @var $zoneMember ZoneMemberInterface */
            $zoneMember = $this->getZoneMemberFactory()->createNew();
            $zoneMember->setCode($memberCode);

            $zone->addMember($zoneMember);
        }

        $this->setReference('Sylius.Zone.'.$name, $zone);

        return $zone;
    }
}
