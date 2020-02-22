<?php

namespace App\Controller;

use App\Api\Kayaposoft\Connector;
use App\Entity\Country;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CountryController extends AbstractController
{

    private $connector;

    public function __construct(Connector $connector)
    {

        $this->connector = $connector;
    }

    public function getCountry(string $countryName): ?Country
    {


        $country = $this->getDoctrine()
            ->getRepository(Country::class)
            ->findOneBy(["country" => $countryName]);
        if (!$country) {

            return null;
        }

        return $country;
    }

    public function getRegion(string $countryRegionCode, Country $country): ?string {
        $region = null;

        foreach ($country->getRegions() as $regionData) {
            if ($countryRegionCode === $regionData["shortCode"]) {
                $region = $regionData["name"];
            }
        }

        return $region;
    }

    public function getTodayType(string $countryCode)
    {

        return $this->connector->fetchTodayType($countryCode);
    }
}
