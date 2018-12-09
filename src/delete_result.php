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

echo "¿Por qué propiedad quiere realizar el borrado?. Escriba 'id' si el borrado se va a hacer por id o 'user' si se va a realizar por user: ";
$handle = fopen("php://stdin", "rb");
$property = fgets($handle);

if (trim($property) === '') {
    echo "ABORTADO! Es obligatorio indicar la propiedad por la que desea eliminar \n";
    exit(0);
} else if (trim($property) === 'id') {
    echo "Indique el id (obligatorio) del result a eliminar: ";
    $value = fgets($handle);
    if (trim($value) === '') {
        echo "ABORTADO! Es obligatorio indicar el id \n";
        exit(0);
    } else if (!is_numeric(trim($value)) || trim($value) <= 0) {
        echo "ABORTADO! No es un valor válido de id. \n";
        exit(0);
    }
} else if (trim($property) === 'user') {
    echo "Indique el userId (obligatorio) del usuario del que quiere eliminar resultados: ";
    $value = fgets($handle);
    if (trim($value) === '') {
        echo "ABORTADO! Es obligatorio indicar el userId \n";
        exit(0);
    } else if (!is_numeric(trim($value)) || trim($value) <= 0) {
        echo "ABORTADO! No es un valor válido de id. \n";
        exit(0);
    }
} else {
    echo "ABORTADO! Propiedad indicada no válida para el borrado \n";
    exit(0);

}
fclose($handle);
echo "\n";
echo "Gracias! Se procede a realizar la operación de eliminación de resultados...\n\n";

/** Se comprueba que exista el resultado que se intenta eliminar */
/** @var Result $result */
$property = trim($property);
$value = trim($value);
if ($property === 'id') {
    $result = $entityManager
        ->getRepository(Result::class)
        ->findOneBy([$property => $value]);
    if (empty($result)) {
        echo "Result con $property = $value no existe en la base de datos." . PHP_EOL;
        exit(0);
    }

    imprimirResultado($result, $argv);

    /** @var  $result */
    try {
        $entityManager->remove($result);
        $entityManager->flush();
        echo "\nEliminado Result " . $property . " = " . $value . PHP_EOL;
    } catch (Exception $exception) {
        echo $exception->getMessage() . PHP_EOL;
    }
}

if ($property === 'user') {
    $user = $entityManager
        ->getRepository(User::class)
        ->findOneBy(['id' => $value]);
    if (empty($user)) {
        echo "Usuario con $property = $value no existe en la base de datos." . PHP_EOL;
        exit(0);
    }

    /** @var  $result */
    $query = $entityManager->createQuery('SELECT r FROM MiW\Results\Entity\Result r WHERE r.user = :user');
    $query->setParameter('user', $value);
    $results = $query->getResult();
    if (empty($results)) {
        echo "Results con $property = $value no existe en la base de datos." . PHP_EOL;
        exit(0);
    }

    imprimirResultados($results, $argv);

    $items = 0;
    foreach ($results as $result) {

        /** @var  $result */
        try {
            $entityManager->remove($result);
            $entityManager->flush();
            $items++;
            echo "\nEliminado Result " . $items . " con " . $property . "=" . $value . PHP_EOL;
        } catch (Exception $exception) {
            echo $exception->getMessage() . PHP_EOL;
        }
    }
}

function imprimirResultado($result, $argv)
{
    echo "Información de Result que va a ser eliminado::: \n\n";

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
}

function imprimirResultados($results, $argv)
{
    echo "Información de Results que van a ser eliminados::: \n\n";

    if (in_array('--json', $argv, true)) {
        echo json_encode($results, JSON_PRETTY_PRINT);
    } else {
        echo PHP_EOL
            . sprintf('%3s - %3s - %22s - %s', 'Id', 'res', 'username', 'time')
            . PHP_EOL;
        $items = 0;
        foreach ($results as $result) {

            echo $result . PHP_EOL;
            $items++;
        }
        echo PHP_EOL . "Total: $items results.\n\n";
    }

}
