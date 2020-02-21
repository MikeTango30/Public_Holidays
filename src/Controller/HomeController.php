<?php

namespace App\Controller;

use App\Entity\Country;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;


class HomeController extends AbstractController
{

    public function index($violations = null)
    {

        $countries = $this->getDoctrine()
            ->getRepository(Country::class)
            ->findAll();

        return $this->render('home/index.html.twig', [
            'countries' => $countries,
            'violations' => $violations
        ]);
    }

    public function showHolidays(Request $request,
                                 HolidaysController $holidaysController,
                                 CountryController $countryController)
    {
        $year = $request->get("year-choice");
        $countryName = $request->get("country-choice");
        $countryRegionCode = $request->get("region-choice");

        // validate input
        $violations = $this->validateInput($year, $countryName, $countryRegionCode);
        if ($violations->count() > 0) {
            return $this->index($violations);
        }


        $country = $countryController->getCountry($countryName);
        $region = $countryRegionCode ? $countryController->getRegion($countryRegionCode, $country) : "";
        $publicHolidays = $holidaysController->getHolidays($country, $year, $countryRegionCode);
        $maxNonWorkdaysInARow = $this->getMaxNonWorkdaysInARow($publicHolidays);
        $holidayCount = $holidaysController->countHolidays($publicHolidays);
        $todayType = $countryController->getTodayType($country->getCountryCode());
        $groupedByMonthHolidays = $holidaysController->groupByMonth($publicHolidays);

        return $this->render('holidays/holidays.html.twig', [
            'holidays' => $groupedByMonthHolidays,
            'todayType' => $todayType,
            'country' => $countryName,
            'year' => $year,
            'region' => $region,
            'holidayCount' => $holidayCount
        ]);
    }

    public function validateInput(string $year = null, string $countryName = null, string $countryRegionCode = null)
    {
        $validator = Validation::createValidator();

        $input = [
            'year' => $year,
            'countryName' => $countryName,
        ];

        $groups = new Assert\GroupSequence(['Default', 'custom']);
        $constraint = new Assert\Collection([
            'year' => [
                new Assert\NotBlank(["message" => "Year field should not be blank"]),
                new Assert\Type(['type' => 'string'])
            ],
            'countryName' => [
                new Assert\NotBlank(["message" => "Country field should not be blank"]),
                new Assert\Type(['type' => 'string'])
            ]
        ]);

        $violations = $validator->validate($input, $constraint, $groups);

        if ($countryRegionCode) {
            $stringConstraint = new Assert\Type(['type' => 'string']);
            $stringConstraint->message = 'Field must be a text';
            $violations = $validator->validate($countryRegionCode, $stringConstraint);
        }

        return $violations;
    }

    public function getMaxNonWorkdaysInARow(array $holidays)
    {

        $tempDates = [];
        $streaks = [];
        $lastDate = null;

        foreach ($holidays as $holidayDate) {
            $date = new DateTime($holidayDate['date']['year'].'-'
                .$holidayDate['date']['month'].'-'
                .$holidayDate['date']['day']);
            // start first streak
            if(empty($tempDates)) {
                $tempDates[] = $date;
            } else {
                $interval = $date->diff($lastDate);
//                $holidayDate['dayOfWeek'];
                // add to streak or store streaks and start new one
                if ($interval->days === 1) {
                    $tempDates[] = $date;
                } else {
                    $streaks[] = $tempDates;
                    $tempDates = [$date];
                }
            }
            $lastDate = $date;
        }
        $streaks[] = $tempDates;

        dd($streaks);
    }
}
//            $holidayDate['day'];
//            $holidayDate['month'];
//            $holidayDate['dayOfWeek'];
//            $holidayDate['year'];