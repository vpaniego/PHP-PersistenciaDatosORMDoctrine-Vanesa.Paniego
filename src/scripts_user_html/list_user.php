<!DOCTYPE html>
<html>
<body>
<h1>Formulario para la consulta de usuarios</h1>
<?php

use MiW\Results\Utils;
use MiW\Results\Entity\User;

require __DIR__ . '/../../vendor/autoload.php';

// Carga las variables de entorno necesarias
$dotenv = new \Dotenv\Dotenv(
    __DIR__ . '/../../',
    Utils::getEnvFileName(__DIR__ . '/../../'));

$dotenv->load();
try {
    $dotenv->required([
        'DATABASE_HOST',
        'DATABASE_NAME',
        'DATABASE_USER',
        'DATABASE_PASSWD',
        'DATABASE_DRIVER'
    ]);
} catch (Exception $exception) {
    die(get_class($exception) . ': ' . $exception->getMessage() . PHP_EOL);
}

// Metodo POST: Se reciben los datos del formulario que habrá que validar
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

// Variables que recogen los datos enviados en el formulario

    $email = isset($_POST['email']) ? $_POST['email'] : null;

    $entityManager = Utils::getEntityManager();

    if (empty($email)) {
        $users = $entityManager
            ->getRepository(User::class)
            ->findAll();
        if (empty($users)) {
            echo '<h3 style="color: #f45642;">' . 'No existen resultados' . '</h3>' . PHP_EOL;
        } else {
            $numrows = sizeof($users);
            // mostramos el resultado
            echo "<h2 align='center'>Resultado de la consulta</h2>\n";
            echo "<table border='1' align='center'>" . PHP_EOL;
            echo "<tr><td colspan='5'>Nº de filas: <em>", $numrows, "</em></td></tr>\n";
            echo "<tr><th> ID </th><th> USERNAME </th><th> EMAIL </th><th> ENABLED </th><th> ISADMIN </th></tr>" . PHP_EOL;
            foreach ($users as $user) {
                $id = $user->getId();
                $username = $user->getUsername();
                $email = $user->getEmail();
                $enabled = $user->isEnabled() == 1 ? 'Si' : 'No';
                $isAdmin = $user->isAdmin() == 1 ? 'Si' : 'No';

                echo <<< ______MARCA
        <tr><td> $id </td>
            <td> $username</td>
            <td> $email</td>
            <td> $enabled </td>
            <td> $isAdmin </td></tr>
______MARCA;
            }
            echo '</table>' . PHP_EOL;
        }
    } else {

        $query = $entityManager->createQuery('SELECT u FROM MiW\Results\Entity\User u WHERE u.email like :email');
        $query->setParameter('email', '%' . $email . '%');
        $users = $query->getResult();

        if (empty($users)) {
            echo '<h3 style="color: #f45642;">' . 'No existen resultados para el EMAIL #' . $email . '</h3>' . PHP_EOL;
        } else {
            $numrows = sizeof($users);
            // mostramos el resultado
            echo "<h2 align='center'>Resultado de la consulta</h2>\n";
            echo "<table border='1' align='center'>" . PHP_EOL;
            echo "<tr><td colspan='5'>Nº de filas: <em>", $numrows, "</em></td></tr>\n";
            echo "<tr><th> ID </th><th> USERNAME </th><th> EMAIL </th><th> ENABLED </th><th> ISADMIN </th></tr>" . PHP_EOL;
            foreach ($users as $user) {
                $id = $user->getId();
                $username = $user->getUsername();
                $email = $user->getEmail();
                $enabled = $user->isEnabled() == 1 ? 'Si' : 'No';
                $isAdmin = $user->isAdmin() == 1 ? 'Si' : 'No';

                echo <<< ______MARCA
        <tr><td> $id </td>
            <td> $username</td>
            <td> $email</td>
            <td> $enabled </td>
            <td> $isAdmin </td></tr>
______MARCA;
            }
            echo '</table>' . PHP_EOL;
        }

    }
}
?>
<!-- Formulario -->
<form method="post">
    <div>
        <p><input type="text" name="email" placeholder="email"></p>
        <p><input type="submit" value="Consultar"></p>
    </div>
</form>

</body>
</html>
