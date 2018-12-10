<!DOCTYPE html>
<html>
<body>
<h1>Formulario para la consulta de resultados por usuario</h1>
<?php

use MiW\Results\Utils;
use MiW\Results\Entity\User;
use MiW\Results\Entity\Result;

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

    $username = isset($_POST['username']) ? $_POST['username'] : null;

    $entityManager = Utils::getEntityManager();

    /** @var Result $result */
    if (empty($username)) {
        $results = $entityManager
            ->getRepository(Result::class)
            ->findAll();
        if (empty($results)) {
            echo '<h3 style="color: #f45642;">' . 'No existen resultados' . '</h3>' . PHP_EOL;
        } else {
            $numrows = sizeof($results);
            // mostramos el resultado
            echo "<h2 align='center'>Resultado de la consulta</h2>\n";
            echo "<table border='1' align='center'>" . PHP_EOL;
            echo "<tr><td colspan='4'>Nº de filas: <em>", $numrows, "</em></td></tr>\n";
            echo "<tr><th> ID </th><th> USER_ID </th><th> RESULTADO </th><th> TIME </th></tr>" . PHP_EOL;
            foreach ($results as $result) {
                $id = $result->getId();
                $user = $result->getUser();
                $value = $result->getResult();
                $time = $result->getTime()->format('Y-m-d H:i:s');

                echo <<< ______MARCA
        <tr><td> $id </td>
            <td> $user</td>
            <td> $value</td>
            <td> $time </td></tr>
______MARCA;
            }
            echo '</table>' . PHP_EOL;
        }
    } else {
        $user = $entityManager
            ->getRepository(User::class)
            ->findOneBy(['username' => $username]);
        if (empty($user)) {
            echo '<h3 style="color: #f45642;">' . 'User con USERNAME #' . $username . ' no encontrado. No es posible mostrar resultados de un usuario inexistente' . '</h3>' . PHP_EOL;
        } else {
            $query = $entityManager->createQuery('SELECT r FROM MiW\Results\Entity\Result r WHERE r.user = :user');
            $query->setParameter('user', $user->getId());
            $results = $query->getResult();

            if (empty($results)) {
                echo '<h3 style="color: #f45642;">' . 'No existen resultados para el USERNAME #' . $username . '</h3>' . PHP_EOL;
            } else {
                $numrows = sizeof($results);
                // mostramos el resultado
                echo "<h2 align='center'>Resultado de la consulta</h2>\n";
                echo "<table border='1' align='center'>" . PHP_EOL;
                echo "<tr><td colspan='4'>Nº de filas: <em>", $numrows, "</em></td></tr>\n";
                echo "<tr><th> ID </th><th> USER_ID </th><th> RESULTADO </th><th> TIME </th></tr>" . PHP_EOL;
                foreach ($results as $result) {
                    $id = $result->getId();
                    $user = $result->getUser();
                    $value = $result->getResult();
                    $time = $result->getTime()->format('Y-m-d H:i:s');

                    echo <<< ______MARCA
        <tr><td> $id </td>
            <td> $user</td>
            <td> $value</td>
            <td> $time </td></tr>
______MARCA;
                }
                echo '</table>' . PHP_EOL;
            }
        }
    }
}
?>
<!-- Formulario -->
<form method="post">
    <div>
        <p><input type="text" name="username" placeholder="username"></p>
        <p><input type="submit" value="Consultar"></p>
    </div>
</form>

</body>
</html>




