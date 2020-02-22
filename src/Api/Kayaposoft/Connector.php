<?php
declare(strict_types=1);

namespace App\Api\Kayaposoft;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

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
    const IS_PUBLIC_HOLIDAY = "/?action=isPublicHoliday&date=%s&country=%s";
    const IS_WORKDAY = "/?action=isWorkDay&date=%s&country=%s";


    public function getHolidaysByYearAndCountry(string $year, string $countryCode): array
    {

        $url = sprintf($_ENV["KAYAPOSOFT_URL"] . self::PUBLIC_HOLIDAYS_BY_YEAR_AND_COUNTRY, $year, $countryCode);

        try{
            $request = $this->client->request("GET", $url);
            $response = $request->getBody()->getContents();
            $response = json_decode($response, true);
        } catch(RequestException $e){
            $response = [
                'error' => "Whoops! Looks like you reaaaally looking for holidays. Try again a few seconds later"
            ];
        }

        return $response;
    }

    public function getHolidaysByYearAndCountryAndRegion(string $year, string $countryCode, string $regionCode): array
    {

        $url = sprintf($_ENV["KAYAPOSOFT_URL"] .
            self::PUBLIC_HOLIDAYS_BY_YEAR_AND_COUNTRY_AND_REGION, $year, $countryCode, $regionCode);

        try{
            $request = $this->client->request("GET", $url);
            $response = $request->getBody()->getContents();
            $response = json_decode($response, true);
        } catch(RequestException $e){
            $response = [
                'error' => "Whoops! Looks like you reaaaally looking for holidays. Try again a few seconds later"
            ];
        }

        return $response;
    }

    public function fetchSupportedCountries(): array
    {

        $url = $_ENV["SUPPORTED_COUNTRIES_URL"];

        try{
            $request = $this->client->request("GET", $url);
            $response = $request->getBody()->getContents();
            $response = json_decode($response, true);
        } catch(RequestException $e){
            $response = [
                'error' => "Whoops! Looks like you reaaaally looking for holidays. Try again a few seconds later"
            ];
        }

        return $response;
    }

    public function isPublicHoliday(string $countryCode): array
    {

        $url = sprintf($_ENV["KAYAPOSOFT_URL"] .
            self::IS_PUBLIC_HOLIDAY, date("d-m-Y"), $countryCode);

        try{
            $request = $this->client->request("GET", $url);
            $response = $request->getBody()->getContents();
            $response = json_decode($response, true);
        } catch(RequestException $e){
            $response = [
                'error' => "Whoops! Looks like you reaaaally looking for holidays. Try again a few seconds later"
            ];
        }

        return $response;
    }

    public function isWorkday(string $countryCode): array
    {

        $url = sprintf($_ENV["KAYAPOSOFT_URL"] .
            self::IS_WORKDAY, date("d-m-Y"), $countryCode);

        try{
            $request = $this->client->request("GET", $url);
            $response = $request->getBody()->getContents();
            $response = json_decode($response, true);
        } catch(RequestException $e){
            $response = [
                'error' => "Whoops! Looks like you reaaaally looking for holidays. Try again a few seconds later"
            ];
        }

        return $response;
    }

    public function fetchTodayType(string $countryCode): array
    {
        $isPublicHoliday = $this->isPublicHoliday($countryCode);
        if (isset($isPublicHoliday['error'])) {
            $today = [
                'error' => $isPublicHoliday['error']
            ];

            return $today;
        }
        $isWorkday = $this->isWorkday($countryCode);
        if (isset($isWorkday['error'])) {
            $today = [
                'error' => $isWorkday['error']
            ];

            return $today;
        }

        $today = [
            'today' => "Workday"
        ];

        if (!$isWorkday['isWorkDay']) {
            $today = [
                'today' => "Free Day"
            ];
        }
        if ($isPublicHoliday['isPublicHoliday']) {
            $today = [
                'today' => "Public Holiday"
            ];
        }

        return $today;
    }
}