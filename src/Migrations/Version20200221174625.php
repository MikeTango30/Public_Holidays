<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Api\Kayaposoft\Connector;
use App\Entity\Country;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Exception\MigrationException;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200221174625 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $client = new Client();
        $connector = new Connector($client);
        $supportedCountries = $connector->fetchSupportedCountries() ?? null;
        $regionRepo = json_decode(file_get_contents(__DIR__ . '/../Repository/RegionData/countriesWithRegionNames.json'), true);

        //get region codes and region names form json repo
        if ($supportedCountries) {
            foreach ($supportedCountries as $countryData) {
                $regions = [];
                if ($countryData["regions"]) {
                    foreach ($regionRepo as $country) {
                        if ($countryData["fullName"] === $country["countryName"]) {
                            $regions = $country["regions"];
                            break;
                        }
                    }
                }
                $this->connection->insert('country', [
                    'country' => $countryData["fullName"],
                    'country_code' => $countryData["countryCode"],
                    'regions' => json_encode($regions)
                    ]);
            }

        }

    }

    public function down(Schema $schema): void
    {
        // TODO: Implement down() method.
    }
}
