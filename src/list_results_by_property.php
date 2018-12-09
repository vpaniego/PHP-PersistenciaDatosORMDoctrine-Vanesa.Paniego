<?php
/**
 * PHP version 7.2
 * src/list_users.php
 *
 * @category Scripts
 * @author   Javier Gil <franciscojavier.gil@upm.es>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://www.etsisi.upm.es ETS de Ingeniería de Sistemas Informáticos
 */

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

echo "¿Por qué propiedad quiere realizar la consulta?. Escriba 'id', 'user' o 'time' para realizar la consulta: ";
$handle = fopen("php://stdin", "rb");
$property = fgets($handle);

if (trim($property) === '') {
    echo "ABORTADO! Es obligatorio indicar la propiedad por la que desea consultar \n";
    exit(0);
} else if (trim($property) === 'id') {
    echo "Indique el id (obligatorio) del result a consultar. Esta consulta devolverá 0 en caso de no haber coincidencia o un único registro correspondiente al valor indicado: ";
    $value = fgets($handle);
    if (trim($value) === '') {
        echo "ABORTADO! Es obligatorio indicar un id \n";
        exit(0);
    } else if (!is_numeric(trim($value)) || trim($value) <= 0) {
        echo "ABORTADO! No es un valor válido. \n";
        exit(0);
    }
} else if (trim($property) === 'user') {
    echo "Indique el userId (obligatorio) del usuario cuyos resultados desea consultar: ";
    $value = fgets($handle);
    if (trim($value) === '') {
        echo "ABORTADO! Es obligatorio indicar un id de usuario \n";
        exit(0);
    } else if (!is_numeric(trim($value)) || trim($value) <= 0) {
        echo "ABORTADO! No es un valor válido. \n";
        exit(0);
    }
} else if (trim($property) === 'time') {
    echo "Indique el time desde (obligatorio) con formato ('Y-m-d H:i:s') por el que desea consultar: ";
    $value = fgets($handle);
    if (trim($value) === '') {
        echo "ABORTADO! Es obligatorio indicar un time desde por el que realizar la consulta \n";
        exit(0);
    } else {
        try {
            $newTimestamp = new DateTime(trim($value));
        } catch (Exception $e) {
            echo $e->getMessage();
            exit(0);
        }
    }
} else {
    echo "ABORTADO! Propiedad indicada no válida para la consulta \n";
    exit(0);

}
fclose($handle);
echo "\n";
echo "Gracias! Se procede a realizar la operación de consulta ...\n\n";


$resultRepository = $entityManager->getRepository(\MiW\Results\Entity\Result::class);

$property = trim($property);
if (strcasecmp(trim($property), 'id') === 0) {
    $value = (trim($value));
    $value = (int)$value;
    $result = $resultRepository
        ->findOneBy([$property => $value]);

    imprimirResultado($result, $argv);

} else if (strcasecmp(trim($property), 'user') === 0) {
    $value = (trim($value));
    $value = (int)$value;
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

} else if (strcasecmp(trim($property), "time") === 0) {
    /** @var  $result */
    $query = $entityManager->createQuery('SELECT r FROM MiW\Results\Entity\Result r WHERE r.time >= :time order by r.time asc');
    $query->setParameter('time', $value);
    $results = $query->getResult();
    if (empty($results)) {
        echo "Results con $property >= $value no existe en la base de datos." . PHP_EOL;
        exit(0);
    }

    imprimirResultados($results, $argv);
}

function imprimirResultado($result, $argv)
{
    echo "Información de Result::: \n\n";

    if (empty($result)) {
        echo PHP_EOL . "Total: 0 results.\n\n";
    } else {
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
}

function imprimirResultados($results, $argv)
{

    echo "Información de Results::: \n\n";

    if (empty($results)) {
        echo PHP_EOL . "Total: 0 results.\n\n";
    } else {
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
}
