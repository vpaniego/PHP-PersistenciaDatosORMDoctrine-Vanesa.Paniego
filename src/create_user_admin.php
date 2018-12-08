<?php
/**
 * PHP version 7.2
 * src\create_user_admin.php
 *
 * @category Utils
 * @package  MiW\Results
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

$user = new User();
$user->setUsername($_ENV['ADMIN_USER_NAME']);
$user->setEmail($_ENV['ADMIN_USER_EMAIL']);
$user->setPassword($_ENV['ADMIN_USER_PASSWD']);
$user->setEnabled(true);
$user->setIsAdmin(true);

try {
    $entityManager->persist($user);
    $entityManager->flush();
    echo 'Created Admin User with ID #' . $user->getId() . PHP_EOL;
} catch (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
}
