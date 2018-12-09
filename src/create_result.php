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

echo "Alta de un nuevo resultado. Por favor complete la siguiente información:\n Introduce value (obligatorio): ";
$newResult = fgets($handle);
echo ">>> Result -> value: " . $newResult;
if (trim($newResult) === '') {
    echo "ABORTADO! Es obligatorio indicar un value \n";
    exit(0);
} else if (!is_numeric(trim($newResult))) {
    echo "ABORTADO! El value indicado no es correcto. Debe ser un valor numérico \n";
    exit(0);
}
echo "Introduce userid (obligatorio): ";
$resultUserId = fgets($handle);
echo ">>> Result -> userId: " . $resultUserId;
if (trim($resultUserId) === '') {
    echo "ABORTADO! Es obligatorio indicar identificador de usuario \n";
    exit(0);
} else if (!is_numeric(trim($resultUserId)) || trim($resultUserId) <= 0) {
    echo "ABORTADO! No es un identificador válido. \n";
    exit(0);
} else {
    /** Se comprueba que exista el usuario al que se intenta crear el resultado */
    /** @var User $user */
    $resultUserId = trim($resultUserId);
    $user = $entityManager
        ->getRepository(User::class)
        ->findOneBy(['id' => $resultUserId]);
    if (empty($user)) {
        echo "Usuario con ID $resultUserId no encontrado." . PHP_EOL;
        exit(0);
    }
}
echo "Introduce time ('Y-m-d H:i:s'): ";
$resultTime = fgets($handle);
if (trim($resultTime) != '') {
    try {
        $newTimestamp = new DateTime(trim($resultTime));
        echo ">>> Result -> time: " . $newTimestamp->format('Y-m-d H:i:sP') . PHP_EOL;
    } catch (Exception $e) {
        echo $e->getMessage();
        exit(0);
    }
} else {
    $newTimestamp = new DateTime('now');
}
fclose($handle);
echo "\n";
echo "Recogidos todos los datos necesarios para el alta de un nuevo resultado.\n\n";

/** @var  $newResult */
$result = new Result((int)$newResult, $user, $newTimestamp);
try {
    $entityManager->persist($result);
    $entityManager->flush();
    echo 'Created Result with ID ' . $result->getId()
        . ' USER ' . $user->getUsername() . PHP_EOL;
} catch (Exception $exception) {
    echo $exception->getMessage();
}

if (in_array('--json', $argv, true)) {
    echo json_encode($result, JSON_PRETTY_PRINT);
} else {
    echo PHP_EOL
        . sprintf('%3s - %3s - %22s - %s', 'Id', 'res', 'username', 'time')
        . PHP_EOL;
    $items = 0;
    /* @var Result $result */
    echo $result . PHP_EOL;
    $items++;

    echo PHP_EOL . "Total: $items results.";
}
