<?php

namespace App\Controller;

use App\Api\Kayaposoft\Connector;
use App\Entity\Country;
use App\Entity\Holidays;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HolidaysController extends AbstractController
{
    private $connector;

    public function __construct(Connector $connector)
    {

        $this->connector = $connector;
    }

    public function groupByMonth(array $holidaysByYear): array
    {

        $grouped = [];

        foreach ($holidaysByYear as $holiday) {
            $grouped[$holiday['date']['month']][] = [
                'date' => $holiday['date'],
                'name' => $holiday['name']
            ];
        }

        return $grouped;
    }

    public function getHolidays(Country $country, string $year): array
    {

        $publicHolidays = $this->getDoctrine()
            ->getRepository(Holidays::class)
            ->findOneBy(["country" => $country, "year" => $year]);
        if ($publicHolidays) {
            $publicHolidays = $publicHolidays->getHolidays();
        }
        if (!$publicHolidays) {
            $publicHolidays = $this->connector->getHolidaysByYearAndCountry($year, $country->getCountryCode());
            $this->createHolidays($year, $country, $publicHolidays);
        }

        return $publicHolidays;
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

    public function countHolidays(array $publicHolidays): int
    {

        return count($publicHolidays);
    }
}
