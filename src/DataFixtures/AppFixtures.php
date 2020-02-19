<?php

namespace App\DataFixtures;

use App\Api\Kayaposoft\Connector;
use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class AppFixtures extends Fixture
{
    private $connector;

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

    public function load(ObjectManager $manager)
    {

        $countries = $this->connector->fetchSupportedCountries();

        if (!$countries) {
            var_dump("API call failed, database not seeded");
        }

        foreach ($countries as $countryData) {
            $country = new Country();
            $country->setCountry($countryData["fullName"]);
            $country->setCountryCode($countryData["countryCode"]);
            $manager->persist($country);
        }

        $manager->flush();
    }
}