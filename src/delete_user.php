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

echo "¿Por qué propiedad quiere realizar el borrado?. Escriba 'id' si el borrado se va a hacer por id o 'username' si se va a realizar por username: ";
$handle = fopen("php://stdin", "rb");
$property = fgets($handle);

if (trim($property) === '') {
    echo "ABORTADO! Es obligatorio indicar la propiedad por la que desea eliminar \n";
    exit(0);
} else if (trim($property) === 'id') {
    echo "Indique el id (obligatorio) del usuario a eliminar: ";
    $value = fgets($handle);
    if (trim($value) === '') {
        echo "ABORTADO! Es obligatorio indicar el id de usuario \n";
        exit(0);
    } else if (!is_numeric(trim($value)) || trim($value) <= 0) {
        echo "ABORTADO! No es un valor válido. \n";
        exit(0);
    }
} else if (trim($property) === 'username') {
    echo "Indique el username (obligatorio) del usuario a eliminar: ";
    $value = fgets($handle);
    if (trim($value) === '') {
        echo "ABORTADO! Es obligatorio indicar el username de usuario \n";
        exit(0);
    }
} else {
    echo "ABORTADO! Propiedad indicada no válida para el borrado \n";
    exit(0);

}
fclose($handle);
echo "\n";
echo "Gracias! Se procede a realizar la operación de eliminación...\n\n";

/** Se comprueba que exista el usuario que se intenta eliminar */
/** @var User $user */
$property = trim($property);
$value = trim($value);
$user = $entityManager
    ->getRepository(User::class)
    ->findOneBy([$property => $value]);
if (empty($user)) {
    echo "Usuario con $property = $value no existe en la base de datos." . PHP_EOL;
    exit(0);
}

echo "Información de User que va a ser eliminado::: \n\n";

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
    echo "\nTotal: 1 users eliminado.\n\n";
}

/** @var  $user */
try {
    $entityManager->remove($user);
    $entityManager->flush();
    echo PHP_EOL . 'Eliminado User ' .$property . ' = ' . $value. PHP_EOL;
} catch (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
}

