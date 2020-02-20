<?php
declare(strict_types=1);

namespace App\Api\Kayaposoft;


use GuzzleHttp\Client;

class Connector
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    //Api url endings
    const PUBLIC_HOLIDAYS_BY_YEAR_AND_COUNTRY =
        "/?action=getHolidaysForYear&year=%s&country=%s&holidayType=public_holiday";
    const PUBLIC_HOLIDAYS_BY_YEAR_AND_COUNTRY_AND_REGION =
        "/?action=getHolidaysForYear&year=%s&country=%s&holidayType=public_holiday&region=%s";
    const IS_PUBLIC_HOLIDAY = "/?action=isWorkDay&date=%s&country=%s";
    const IS_WORKDAY = "/?action=isWorkDay&date=%s&country=%s";


    public function getHolidaysByYearAndCountry(string $year, string $countryCode): array
    {

        $url = sprintf($_ENV["KAYAPOSOFT_URL"] . self::PUBLIC_HOLIDAYS_BY_YEAR_AND_COUNTRY, $year, $countryCode);

        $request = $this->client->request("GET", $url);
        $response = $request->getBody()->getContents();

        return json_decode($response, true);
    }

    public function getHolidaysByYearAndCountryAndRegion(string $year, string $countryCode, string $regionCode): array
    {

        $url = sprintf($_ENV["KAYAPOSOFT_URL"] .
            self::PUBLIC_HOLIDAYS_BY_YEAR_AND_COUNTRY_AND_REGION, $year, $countryCode, $regionCode);
        $request = $this->client->request("GET", $url);
        $response = $request->getBody()->getContents();

        return json_decode($response, true);
    }

    public function fetchSupportedCountries(): array
    {

        $request = $this->client->request("GET", $_ENV["SUPPORTED_COUNTRIES_URL"]);
        $response = $request->getBody()->getContents();

        return json_decode($response, true);
    }

    public function isPublicHoliday(string $countryCode): array
    {

        $url = sprintf($_ENV["KAYAPOSOFT_URL"] .
            self::IS_PUBLIC_HOLIDAY, date("d-m-Y"), $countryCode);

        $request = $this->client->request("GET", $url);
        $response = $request->getBody()->getContents();

        return json_decode($response, true);
    }

    public function isWorkday(string $countryCode): array
    {

        $url = sprintf($_ENV["KAYAPOSOFT_URL"] .
            self::IS_WORKDAY, date("d-m-Y"), $countryCode);

        $request = $this->client->request("GET", $url);
        $response = $request->getBody()->getContents();

        return json_decode($response, true);
    }

    public function fetchTodayType(string $countryCode): string
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
}