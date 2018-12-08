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

if ($argc < 5 || $argc > 6) {
    $fich = basename(__FILE__);
    echo <<< MARCA_FIN

    Usage: $fich <Username> <Email> <Password> <Enabled> [<Admin>]

MARCA_FIN;
    exit(0);
}

$username = isset($argv[1]) ? (string)$argv[1] : "";
$email = isset($argv[2]) ? (string)$argv[2] : "";
$password = isset($argv[3]) ? (string)$argv[3] : "";
$enabled = isset($argv[4]) ? (boolean)$argv[4] : 1;
$isAdmin = isset($argv[5]) ? (boolean)$argv[5] : 0;


/* Data validations */
if (empty($username) || strcmp($username, "''") === 0) {
    echo "El campo Username es obligatorio y no puede ser vacío. Por favor, indique un valor correcto." . PHP_EOL;
    exit(0);
}

if (empty($email) || strcmp($email, "''") === 0) {
    echo "El campo Email es obligatorio y no puede ser vacío. Por favor, indique un valor correcto." . PHP_EOL;
    exit(0);
} else {
    if (false == filter_var($email, FILTER_VALIDATE_EMAIL)){
        echo "El campo Email no tiene un formato valido. Por favor, indique un valor correcto." . PHP_EOL;
        exit(0);
    }
}

if (empty($password) || strcmp($password, "''") === 0) {
    echo "El campo Password es obligatorio y no puede ser vacío. Por favor, indique un valor correcto." . PHP_EOL;
    exit(0);
}

/*if (empty($enabled)) {
    echo "El campo Enabled es obligatorio y no puede ser vacío. Por favor, indique un valor correcto" . PHP_EOL;
    exit(0);
}*/

/** Se comprueba que no exista ya el usuario que se intenta crear */
/** @var User $user */
$user = $entityManager
    ->getRepository(User::class)
    ->findOneBy(['username' => $username]);
if (!empty($user)) {
    echo "Usuario $username ya existe en la base de datos." . PHP_EOL;
    exit(0);
}

/** @var  $newUser */
$newUser = new User($username, $email, $password, $enabled, $isAdmin);

try {
    $entityManager->persist($newUser);
    $entityManager->flush();
    echo 'Created User with ID #' . $newUser->getId() . PHP_EOL;
} catch (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
}
