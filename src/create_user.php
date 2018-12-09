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

echo "Introduce username (obligatorio): ";
$username = fgets($handle);
echo ">>> username: " . $username;
if (trim($username) === '') {
    echo "ABORTADO! Es obligatorio indicar un username \n";
    exit(0);
} else {
    /** Se comprueba que no exista ya el usuario que se intenta crear */
    /** @var User $user */
    $username = trim($username);
    $user = $entityManager
        ->getRepository(User::class)
        ->findOneBy(['username' => $username]);
    if (!empty($user)) {
        echo "Usuario $username ya existe en la base de datos." . PHP_EOL;
        exit(0);
    }
}
echo "Introduce email (obligatorio): ";
$email = fgets($handle);
echo ">>> email: " . $email;
if (trim($email) === '') {
    echo "ABORTADO! Es obligatorio indicar email \n";
    exit(0);
} else if (false == filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
    echo "El campo Email no tiene un formato valido. Por favor, indique un valor correcto." . PHP_EOL;
    exit(0);
}

echo "Introduce password (obligatorio): ";
$password = fgets($handle);
echo ">>> password: " . $password;
if (trim($password) === '') {
    echo "ABORTADO! Es obligatorio indicar password \n";
    exit(0);
}

echo "Introduce enabled (obligatario): ";
$enabled = fgets($handle);
echo ">>> enabled: " . $enabled;
if (trim($enabled) === '') {
    echo "ABORTADO! Es obligatorio indicar enabled \n";
    exit(0);
} else if (!is_numeric(trim($enabled)) || trim($enabled) < 0 || trim($enabled) > 1) {
    echo "ABORTADO! No es un valor válido. Debe estar entre 0 y 1 \n";
    exit(0);
}

echo "Introduce isAdmin: ";
$isAdmin = fgets($handle);
echo ">>> isAdmin: " . $isAdmin;
if (trim($isAdmin) !== '' && !is_numeric(trim($isAdmin)) || trim($isAdmin) < 0 || trim($isAdmin) > 1) {
    echo "ABORTADO! No es un valor válido. Debe estar entre 0 y 1 \n";
    exit(0);
}

fclose($handle);
echo "\n";
echo "Recogidos todos los datos necesarios para el alta de un nuevo usuario.\n\n";


/** @var  $newUser */
$newUser = new User($username, trim($email), trim($password), trim($enabled), trim($isAdmin));

try {
    $entityManager->persist($newUser);
    $entityManager->flush();
    echo 'Created User with ID #' . $newUser->getId() . PHP_EOL;
} catch (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
}

if (in_array('--json', $argv, true)) {
    echo json_encode($newUser, JSON_PRETTY_PRINT);
} else {

    echo PHP_EOL . sprintf(
            '  %2s: %20s %30s %7s' . PHP_EOL,
            'Id', 'Username:', 'Email:', 'Enabled:'
        );
    /** @var User $newUser */
    echo sprintf(
        '- %2d: %20s %30s %7s',
        $newUser->getId(),
        $newUser->getUsername(),
        $newUser->getEmail(),
        ($newUser->isEnabled()) ? 'true' : 'false'
    ),
    PHP_EOL;
    echo "\nTotal: 1 users actualizado.\n\n";
}
