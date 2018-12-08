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

if ($argc < 3 || $argc > 3) {
    $fich = basename(__FILE__);
    echo <<< MARCA_FIN

    Usage: $fich <id> <value> || <username> <value>
    NOTE: User sólo se puede eliminar o por id o por username. Ninguna otra propiedad permite la eliminación de users. 

MARCA_FIN;
    exit(0);
}

$property = isset($argv[1]) ? $argv[1] : "";
$value = $argv[2];

/* Data validations */
if (empty($property) || strcmp($property, "''") === 0) {
    echo "Es obligatorio indicar la propiedad por la que desea eliminar un usuario. Puede ser por id o por username. Por favor, indique un valor correcto." . PHP_EOL;
    exit(0);
}

if (strcasecmp($property, "id") !== 0 && strcasecmp($property, "username") !== 0) {
    echo "La propiedad por la que está intentando eliminar no es una propiedad válida. Por favor, indique un valor correcto." . PHP_EOL;
    exit(0);
} else if(strcasecmp($property, "id") === 0) {
    if (!is_numeric($value) || $value <= 0) {
        echo "Es obligatorio indicar un id de usuario válido para poder eliminarlo. Por favor, indique un valor correcto." . PHP_EOL;
        exit(0);
    }
}

/** Se comprueba que exista el usuario que se intenta eliminar */
/** @var User $user */
$user = $entityManager
    ->getRepository(User::class)
    ->findOneBy([$property => $value]);
if (empty($user)) {
    echo "Usuario con $property = $value no existe en la base de datos." . PHP_EOL;
    exit(0);
}

/** @var  $user */
try {
    $entityManager->remove($user);
    $entityManager->flush();
    echo 'Eliminado User ' .$property . ' = ' . $value. PHP_EOL;
} catch (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
}