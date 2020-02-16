<?php
declare(strict_types=1);

namespace App\Api\Kayaposoft;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class Connector
{
    private $supportedCountries;

    //Api url endings
    const PUBLIC_HOLIDAYS_BY_YEAR_AND_COUNTRY =
        "/?action=getHolidaysForYear&year=%s&country=%s&holidayType=public_holiday";
    const PUBLIC_HOLIDAYS_BY_YEAR_AND_COUNTRY_AND_REGION =
        "/?action=getHolidaysForYear&year=%s&country=%s&holidayType=public_holiday&region=%s";
    const IS_PUBLIC_HOLIDAY = "/?action=isWorkDay&date=%s&country=%s";
    const IS_WORKDAY = "/?action=isWorkDay&date=%s&country=%s";

    public function __construct()
    {
        $this->supportedCountries = $this->fetchSupportedCountries();
    }

    public function getHolidaysByYearAndCountry(string $year, string $countryCode) : array
    {
        $client = new Client;

        $url = sprintf($_ENV["KAYAPOSOFT_URL"] . self::PUBLIC_HOLIDAYS_BY_YEAR_AND_COUNTRY, $year, $countryCode);

        $request = $client->request("GET", $url);
        $response = $request->getBody()->getContents();

        return json_decode($response, true);
    }

    public function getHolidaysByYearAndCountryAndRegion(string $year, string $countryCode, string $region) : array
    {
        $client = new Client;

        $url = sprintf($_ENV["KAYAPOSOFT_URL"] .
            self::PUBLIC_HOLIDAYS_BY_YEAR_AND_COUNTRY_AND_REGION, $year, $countryCode, $region);

        $request = $client->request("GET", $url);
        $response = $request->getBody()->getContents();

        return json_decode($response, true);
    }

    /**
     * @return mixed
     */
    public function getSupportedCountries() :array
    {
        return $this->supportedCountries;
    }


    public function fetchSupportedCountries() : array
    {
        $client = new Client;

        $request = $client->request("GET", $_ENV["SUPPORTED_COUNTRIES_URL"]);
        $response = $request->getBody()->getContents();

        return json_decode($response, true);
    }

    public function isPublicHoliday(string $countryCode) : array
    {
        $client = new Client;

        $url = sprintf($_ENV["KAYAPOSOFT_URL"] .
            self::IS_PUBLIC_HOLIDAY, date("d-m-Y"), $countryCode);

        $request = $client->request("GET", $url);
        $response = $request->getBody()->getContents();

        return json_decode($response, true);
    }

    public function isWorkday(string $countryCode) : array
    {
        $client = new Client;

        $url = sprintf($_ENV["KAYAPOSOFT_URL"] .
            self::IS_WORKDAY, date("d-m-Y"), $countryCode);

        $request = $client->request("GET", $url);
        $response = $request->getBody()->getContents();

        return json_decode($response, true);
    }

    public function today(string $countryCode) : string
    {
        $today = "Workday";
        $isPublicHoliday = $this->isPublicHoliday($countryCode);
        $isWorkday = $this->isWorkday($countryCode);

        if (!$isPublicHoliday && !$isWorkday) {
            $today = "Free day";
        }
        if ($isPublicHoliday) {
            $today = "Holiday";
        }

        return $today;
    }

    public function groupByMonth(array $holidaysByYear) : array
    {
        $grouped = [];

        foreach ($holidaysByYear as $holiday) {
            $grouped[$holiday['date']['month']][] = [
                'date' => $holiday['date'],
                'name' => $holiday['name']
            ];
        }
//        dd($grouped);
        return $grouped;
    }
}