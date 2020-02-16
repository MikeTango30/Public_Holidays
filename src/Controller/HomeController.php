<?php

namespace App\Controller;

use App\Api\Kayaposoft\Connector;
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
        $country = $request->get("country-choice");
        $countryCode = $this->getCountryCode($country, $connector);

        $publicHolidays = $connector->getHolidaysByYearAndCountry($year, $countryCode);
        $holidayCount = count($publicHolidays);
        $groupedByMonthHolidays = $connector->groupByMonth($publicHolidays);

        $todayType = $connector->today($countryCode);

        return $this->render('home/holidays.html.twig', [
            'holidays' => $groupedByMonthHolidays,
            'todayType' => $todayType,
            'country' => $country,
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
        foreach ($holidays as $holiday) {

        }
    }
}
