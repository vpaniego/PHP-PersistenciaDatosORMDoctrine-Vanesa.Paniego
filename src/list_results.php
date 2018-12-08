<?php
/**
 * PHP version 7.2
 * src/list_results.php
 *
 * @category Scripts
 * @author   Javier Gil <franciscojavier.gil@upm.es>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://www.etsisi.upm.es ETS de Ingeniería de Sistemas Informáticos
 */

use MiW\Results\Entity\Result;
use MiW\Results\Utils;

require __DIR__ . '/../vendor/autoload.php';

// Carga las variables de entorno
$dotenv = new \Dotenv\Dotenv(
    __DIR__ . '/..',
    Utils::getEnvFileName(__DIR__ . '/..')
);
$dotenv->load();

$entityManager = Utils::getEntityManager();

$resultsRepository = $entityManager->getRepository(Result::class);
$results = $resultsRepository->findAll();

if ($argc === 1) {
    echo PHP_EOL
        . sprintf('%3s - %3s - %22s - %s', 'Id', 'res', 'username', 'time')
        . PHP_EOL;
    $items = 0;
    /* @var Result $result */
    foreach ($results as $result) {
        echo $result . PHP_EOL;
        $items++;
    }
    echo PHP_EOL . "Total: $items results.";
} elseif (in_array('--json', $argv, true)) {
    echo json_encode($results, JSON_PRETTY_PRINT);
}
