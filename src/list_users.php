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

$userRepository = $entityManager->getRepository(User::class);
$users = $userRepository->findAll();

if (in_array('--json', $argv, true)) {
    echo json_encode($users, JSON_PRETTY_PRINT);
} else {
    $items = 0;
    echo PHP_EOL . sprintf(
        '  %2s: %20s %30s %7s' . PHP_EOL,
        'Id', 'Username:', 'Email:', 'Enabled:'
    );
    /** @var User $user */
    foreach ($users as $user) {
        echo sprintf(
            '- %2d: %20s %30s %7s',
            $user->getId(),
            $user->getUsername(),
            $user->getEmail(),
            ($user->isEnabled()) ? 'true' : 'false'
        ),
        PHP_EOL;
        $items++;
    }

    echo "\nTotal: $items users.\n\n";
}
