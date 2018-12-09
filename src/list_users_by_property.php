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

echo "¿Por qué propiedad quiere realizar la consulta?. Escriba 'id', 'username' o 'email' para realizar la consulta: ";
$handle = fopen("php://stdin", "rb");
$property = fgets($handle);

if (trim($property) === '') {
    echo "ABORTADO! Es obligatorio indicar la propiedad por la que desea consultar \n";
    exit(0);
} else if (trim($property) === 'id') {
    echo "Indique el id (obligatorio) del usuario a consultar. Esta consulta devolverá 0 en caso de no haber coincidencia o un único registro correspondiente al valor indicado: ";
    $value = fgets($handle);
    if (trim($value) === '') {
        echo "ABORTADO! Es obligatorio indicar un id de usuario \n";
        exit(0);
    } else if (!is_numeric(trim($value)) || trim($value) <= 0) {
        echo "ABORTADO! No es un valor válido. \n";
        exit(0);
    }
} else if (trim($property) === 'username') {
    echo "Indique el username (obligatorio) del usuario a consultar. Esta consulta devolverá 0 en caso de no haber coincidencia o un único registro correspondiente al valor indicado: ";
    $value = fgets($handle);
    if (trim($value) === '') {
        echo "ABORTADO! Es obligatorio indicar un username de usuario \n";
        exit(0);
    }
} else if (trim($property) === 'email') {
    echo "Indique el email (obligatorio) a consultar. Esta consulta devolverá tantos registros como valores contengan el valor indicado: ";
    $value = fgets($handle);
    if (trim($value) === '') {
        echo "ABORTADO! Es obligatorio indicar un email de usuario \n";
        exit(0);
    }
} else {
    echo "ABORTADO! Propiedad indicada no válida para la consulta \n";
    exit(0);

}
fclose($handle);
echo "\n";
echo "Gracias! Se procede a realizar la operación de consulta ...\n\n";



$userRepository = $entityManager->getRepository(User::class);

$property = trim($property);
$value = trim($value);
if (strcasecmp(trim($property) ,"id") === 0 || strcasecmp(trim($property), "username") === 0) {

    $user = $entityManager
        ->getRepository(User::class)
        ->findOneBy([$property => $value]);

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
    $query->setParameter($property, '%' . $value . '%');
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
