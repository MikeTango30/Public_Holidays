<?php

namespace App\Controller;

use App\Entity\Country;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HomeController extends AbstractController
{

    public function index()
    {
        $countries = $this->getDoctrine()
            ->getRepository(Country::class)
            ->findAll();

        return $this->render('home/index.html.twig', [
            'countries' => $countries,
        ]);
    }

    public function showHolidays(Request $request, HolidaysController $holidaysController, CountryController $countryController)
    {

        $year = $request->get("year-choice");
        $countryName = $request->get("country-choice");
        $countryRegionCode = $request->get("region-choice");
//        dd($request);
        $country = $countryController->getCountry($countryName);
        $publicHolidays = $holidaysController->getHolidays($country, $year, $countryRegionCode);

        $holidayCount = $holidaysController->countHolidays($publicHolidays);
        $todayType = $countryController->getTodayType($country->getCountryCode());
        $groupedByMonthHolidays = $holidaysController->groupByMonth($publicHolidays);

        return $this->render('home/holidays.html.twig', [
            'holidays' => $groupedByMonthHolidays,
            'todayType' => $todayType,
            'country' => $countryName,
            'year' => $year,
            'holidayCount' => $holidayCount
        ]);
    }

    public function getMaxNonWorkdaysInARow(array $holidays)
    {

        $count = 0;
        //TODO
    }
}
