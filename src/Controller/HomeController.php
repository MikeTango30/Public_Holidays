<?php

namespace App\Controller;

use App\Api\Kayaposoft\Connector;
use App\Entity\Country;
use App\Entity\Holidays;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;


class HomeController extends AbstractController
{

    public function index(Connector $connector)
    {
        $countries = $connector->getSupportedCountries();

        return $this->render('home/index.html.twig', [
            'countries' => $countries,
        ]);
    }

    public function showHolidays(Request $request, Connector $connector)
    {

        $year = $request->get("year-choice");
        $countryName = $request->get("country-choice");
        $countryCode = $this->getCountryCode($countryName, $connector);

        $country = $this->getDoctrine()
            ->getRepository(Country::class)
            ->findOneBy(["country_code" => $countryCode]);
        if (!$country) {
            throw $this->createNotFoundException(
                'No country found for country code '.$countryCode
            );
        }

        $publicHolidays = $this->getDoctrine()
            ->getRepository(Holidays::class)
            ->findOneBy(["country" => $country])->getHolidays();
        if (!$publicHolidays) {
            $publicHolidays = $connector->getHolidaysByYearAndCountry($year, $countryCode);
            $this->createHolidays($year, $country, $publicHolidays);
        }



        $holidayCount = count($publicHolidays);
        $groupedByMonthHolidays = $connector->groupByMonth($publicHolidays);

        $todayType = $connector->today($countryCode);

        return $this->render('home/holidays.html.twig', [
            'holidays' => $groupedByMonthHolidays,
            'todayType' => $todayType,
            'country' => $countryName,
            'year' => $year,
            'holidayCount' => $holidayCount
        ]);
    }

    public function getCountryCode(string $country, Connector $connector) : string
    {
        $countryCode = "";
        $countries = $connector->getSupportedCountries();

        foreach ($countries as $countryItems) {
            if(array_search($country, $countryItems)) {
                $countryCode = $countryItems["countryCode"];
                break;
            }
        }

        return $countryCode;
    }

    public function getMaxNonWorkdaysInARow(array $holidays) {
        $count = 0;
        //TODO
    }

    public function createHolidays(string $year, Country $country, array $holidaysJson)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $holidays = new Holidays();
        $holidays->setCountry($country);
        $holidays->setYear($year);
        $holidays->setHolidays($holidaysJson);

        $entityManager->persist($holidays);

        $entityManager->flush();

    }
}
