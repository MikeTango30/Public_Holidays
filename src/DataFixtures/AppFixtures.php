<?php

namespace App\DataFixtures;

use App\Api\Kayaposoft\Connector;
use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class AppFixtures extends Fixture
{
    private $connector;
    private $supportedCountries;
    private $regionRepo;

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
        $this->supportedCountries = $this->connector->fetchSupportedCountries() ?? null;
        $this->regionRepo = json_decode(file_get_contents(__DIR__ . '/../Repository/RegionData/countriesWithRegionNames.json'), true);
    }

    public function load(ObjectManager $manager)
    {
        //get region codes and region names form json repo
        if ($this->supportedCountries) {
            foreach ($this->supportedCountries as $countryData) {
                $regions = [];
                if ($countryData["regions"]) {
                    foreach ($this->regionRepo as $country) {
                        if ($countryData["fullName"] === $country["countryName"]) {
                            $regions = $country["regions"];
                            break;
                        }
                    }
                }

                $country = new Country();
                $country->setCountry($countryData["fullName"]);
                $country->setCountryCode($countryData["countryCode"]);
                $country->setRegions($regions);
                $manager->persist($country);

            }

            $manager->flush();
        }
    }


}