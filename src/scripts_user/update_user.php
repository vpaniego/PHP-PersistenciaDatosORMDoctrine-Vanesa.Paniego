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

require __DIR__ . '/../../vendor/autoload.php';

// Carga las variables de entorno
$dotenv = new \Dotenv\Dotenv(
    __DIR__ . '/../../',
    Utils::getEnvFileName(__DIR__  . '/../../')
);
$dotenv->load();

$entityManager = Utils::getEntityManager();

$handle = fopen("php://stdin", "rb");
echo "Introduce id (obligatorio): ";
$userId = fgets($handle);
echo ">>> id: " . $userId;
if(trim($userId) === ''){
    echo "ABORTADO! Es obligatorio indicar un id válido \n";
    exit(0);
} else if (!is_numeric(trim($userId)) || trim($userId) <= 0) {
    echo "ABORTADO! No es un valor válido. \n";
    exit(0);
} else {
    echo "Introduce username: ";
    $username = fgets($handle);
    echo ">>> username: " . $username;

    echo "Introduce email: ";
    $email = fgets($handle);
    echo ">>> email: " . $email;
    if (trim($email) != '' && false == filter_var(trim($email), FILTER_VALIDATE_EMAIL)){
        echo "El campo Email no tiene un formato valido. Por favor, indique un valor correcto." . PHP_EOL;
        exit(0);
    }

    echo "Introduce password: ";
    $password = fgets($handle);
    echo ">>> password: " . $password;

    echo "Introduce enabled: ";
    $enabled = fgets($handle);
    echo ">>> enabled: " . $enabled;
    if (trim($enabled) != '' && (!is_numeric(trim($enabled)) || trim($enabled) < 0 || trim($enabled) > 1)) {
        echo "ABORTADO! No es un valor válido. Debe estar entre 0 y 1 \n";
        exit(0);
    }

    echo "Introduce isAdmin: ";
    $isAdmin = fgets($handle);
    echo ">>> isAdmin: " . $isAdmin;
    if (trim($isAdmin) != '' && (!is_numeric(trim($isAdmin)) || trim($isAdmin) < 0 || trim($isAdmin) > 1)) {
        echo "ABORTADO! No es un valor válido. Debe estar entre 0 y 1 \n";
        exit(0);
    }
}
fclose($handle);
echo "\n";
echo "Gracias! Se procede a realizar la operación de modificación...\n\n";


/** Se comprueba que no exista ya el usuario que se intenta modificar */
/** @var User $user */
$userId = trim($userId);
$user = $entityManager
    ->getRepository(User::class)
    ->findOneBy(['id' => $userId]);
if (empty($user)) {
    echo "Usuario con ID $userId no encontrado." . PHP_EOL;
    exit(0);
}

/** @var  $newUser */
if(trim($username) !== '') {
    $user->setUsername(trim($username));
}
if(trim($email) !== '') {
    $user->setEmail(trim($email));
}
if(trim($password) !== '') {
    $user->setPassword(trim($password));
}
if(trim($enabled) !== '') {
    $user->setEnabled(trim($enabled));
}
if(trim($isAdmin) !== '') {
    $user->setIsAdmin(trim($isAdmin));
}

try {
    $entityManager->persist($user);
    $entityManager->flush();
    echo 'Actualizado User with ID #' . $user->getId() . PHP_EOL;
} catch (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
}

if (in_array('--json', $argv, true)) {
    echo json_encode($user, JSON_PRETTY_PRINT);
} else {

    echo PHP_EOL . sprintf(
            '  %2s: %20s %30s %7s' . PHP_EOL,
            'Id', 'Username:', 'Email:', 'Enabled:'
        );
    /** @var User $user */
    echo sprintf(
        '- %2d: %20s %30s %7s',
        $user->getId(),
        $user->getUsername(),
        $user->getEmail(),
        ($user->isEnabled()) ? 'true' : 'false'
    ),
    PHP_EOL;
    echo "\nTotal: 1 users actualizado.\n\n";
}



