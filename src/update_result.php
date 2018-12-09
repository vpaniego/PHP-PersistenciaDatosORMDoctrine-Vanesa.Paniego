<?php
/**
 * PHP version 7.2
 * src\create_result.php
 *
 * @category Utils
 * @package  MiW\Results
 * @author   Javier Gil <franciscojavier.gil@upm.es>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://www.etsisi.upm.es ETS de Ingeniería de Sistemas Informáticos
 */

use MiW\Results\Entity\Result;
use MiW\Results\Entity\User;
use MiW\Results\Utils;

require __DIR__ . '/../vendor/autoload.php';

// Carga las variables de entorno
$dotenv = new \Dotenv\Dotenv(
    __DIR__ . '/..',
    Utils::getEnvFileName(__DIR__ . '/..')
);
$dotenv->load();

$entityManager = Utils::getEntityManager();

$handle = fopen("php://stdin", "rb");
echo "Para realizar la modificación de un Result debes introducir un id (obligatorio): ";
$resultId = fgets($handle);
echo ">>> id: " . $resultId;
if (trim($resultId) === '') {
    echo "ABORTADO! Es obligatorio indicar un id válido \n";
    exit(0);
} else if (!is_numeric(trim($resultId)) || trim($resultId) <= 0) {
    echo "ABORTADO! No es un valor válido. \n";
    exit(0);
} else {
    echo "Introduce value: ";
    $resultValue = fgets($handle);
    if (trim($resultValue) != '') {
        if(!is_numeric(trim($resultValue))){
            echo "ABORTADO! Es obligatorio indicar un valor numérico \n";
            exit(0);
        }
    }
    echo "Introduce time ('Y-m-d H:i:s'): ";
    $resultTime = fgets($handle);
    if (trim($resultTime) != '') {
        try {
            $newTimestamp = new DateTime(trim($resultTime));
            echo ">>> time: " . $newTimestamp->format('Y-m-d H:i:sP') . PHP_EOL;
        } catch (Exception $e) {
            echo $e->getMessage();
            exit(0);
        }
    } else {
        $newTimestamp;

    }
}
fclose($handle);
echo "\n";
echo "Gracias! Se procede a realizar la operación de modificación...\n\n";

/** Se comprueba que no exista ya el result que se intenta modificar */
/** @var Result $result */
$resultId = trim($resultId);
$result = $entityManager
    ->getRepository(Result::class)
    ->findOneBy(['id' => $resultId]);
if (empty($result)) {
    echo "Result con ID $resultId no encontrado." . PHP_EOL;
    exit(0);
}

/** @var  $newResult */
if (trim($resultValue) !== '') {
    $resultValue = trim($resultValue);
    $result->setResult((int)$resultValue);
}
if (!empty($newTimestamp)) {
    $result->setTime($newTimestamp);
}

try {
    $entityManager->persist($result);
    $entityManager->flush();
    echo 'Actualizado Result with ID #' . $result->getId() . PHP_EOL;
} catch (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
}

if (in_array('--json', $argv, true)) {
    echo json_encode($result, JSON_PRETTY_PRINT);
} else {
    echo PHP_EOL
        . sprintf('%3s - %3s - %22s - %s', 'Id', 'res', 'username', 'time')
        . PHP_EOL;
    $items = 0;
    echo $result . PHP_EOL;
    $items++;

    echo PHP_EOL . "Total: $items results.\n\n";
}



