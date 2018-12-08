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

if ($argc < 3 || $argc > 4) {
    $fich = basename(__FILE__);
    echo <<< MARCA_FIN

    Usage: $fich <Property> <Value> [<--json>] 
    Note: Consulta por <Property> id o username devuelve un único elemento. Consulta por <Property> email o enabled devuelve una lista de elementos.
    Example: 
            >>> php list_users_by_property.php id 999 ----> 0 ó un único resultado para la búsqueda por 'id = 999'
            >>> php list_users_by_property.php username userX ----> 0 ó un único resultado para la búsqueda por 'username = usernameX'
            >>> php list_users_by_property.php email @xxx.es ---> Tantos resultados como valores coincidan con 'email contains @xxx.es' 
    

MARCA_FIN;
    exit(0);
}

$userRepository = $entityManager->getRepository(User::class);

$property = isset($argv[1]) ? (string)$argv[1] : "";
$valor = isset($argv[2]) ? (string)$argv[2] : "";

/* Data validations */
if (empty($property) || strcmp($property, "''") === 0) {
    echo "Es obligatorio indicar la propiedad por la que desea consultar un usuario. Puede ser por id, por username o por email. Por favor, indique un valor correcto." . PHP_EOL;
    exit(0);
}

if (empty($valor) || strcmp($valor, "''") === 0) {
    echo "Es obligatorio indicar el valor por el que desea realizar la consulta. Por favor, indique un valor correcto." . PHP_EOL;
    exit(0);
}

if (strcasecmp($property, "id") !== 0 && strcasecmp($property, "username") !== 0
    && strcasecmp($property, "email") !== 0) {
    echo "La propiedad por la que está intentando consultar no es valida para búsqueda. Por favor, indique un valor correcto." . PHP_EOL;
    exit(0);
} else if (strcasecmp($property, "id") === 0 || strcasecmp($property, "username") === 0) {

    $user = $entityManager
        ->getRepository(User::class)
        ->findOneBy([$property => $valor]);

    if (in_array('--json', $argv, true)) {
        if (!empty($user)) {
            echo json_encode($user, JSON_PRETTY_PRINT);
        } else {
            echo "{}";
        }
    } else if (!empty($user)) {
        $items = 0;
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
        $items++;
        echo "\nTotal: $items users.\n\n";
    } else {
        echo "\nTotal: 0 users.\n\n";
    }

} else {
    /*
       Utilizando criteria devuelve datos y se imprimen pero
       si quieres verlos en formato --json, el resultado es []

       $criteria = new \Doctrine\Common\Collections\Criteria();
       $criteria->where($criteria->expr()->contains($property, $valor));

       $users = $userRepository->matching($criteria);
    */

    $query = $entityManager->createQuery('SELECT u FROM MiW\Results\Entity\User u WHERE u.' . $property . ' like :' . $property);
    $query->setParameter($property, '%' . $valor . '%');
    $users = $query->getResult();

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
}
