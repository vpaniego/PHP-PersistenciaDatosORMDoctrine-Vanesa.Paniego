<?php
/**
 * PHP version 7.2
 * src\cli-config.php
 *
 * @category Utils
 * @package  MiW/Results
 * @author   Javier Gil <franciscojavier.gil@upm.es>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://www.etsisi.upm.es ETS de Ingeniería de Sistemas Informáticos
 */

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use MiW\Results\Utils;

// Carga las variables de entorno necesarias
$dotenv = new \Dotenv\Dotenv(__DIR__, Utils::getEnvFileName(__DIR__));
$dotenv->load();
try {
    $dotenv->required([
        'DATABASE_HOST',
        'DATABASE_NAME',
        'DATABASE_USER',
        'DATABASE_PASSWD',
        'DATABASE_DRIVER'
    ]);
} catch (Exception $exception) {
    die(get_class($exception) . ': ' . $exception->getMessage() . PHP_EOL);
}

$entityManager = Utils::getEntityManager();

return ConsoleRunner::createHelperSet($entityManager);
