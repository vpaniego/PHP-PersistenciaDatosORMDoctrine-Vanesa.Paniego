<!DOCTYPE html>
<html>
<body>
<h1>Formulario de alta para nuevos usuarios</h1>
<?php

use MiW\Results\Utils;
use MiW\Results\Entity\User;

require __DIR__ . '/../../vendor/autoload.php';

// Carga las variables de entorno necesarias
$dotenv = new \Dotenv\Dotenv(
    __DIR__ . '/../../',
    Utils::getEnvFileName(__DIR__  . '/../../'));

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

    /**
     * Método que valida si un texto no esta vacío
     * @param {string} - Texto a validar
     * @return {boolean}
     */
    function validar_requerido(string $texto): bool
    {
        return !(trim($texto) == '');
    }

    /**
     * Método que valida si es un número entero
     * @param {string} - Número a validar
     * @return {bool}
     */
    function validar_entero(string $numero): bool
    {
        return (filter_var($numero, FILTER_VALIDATE_INT) === FALSE) ? False : True;
    }

    /**
     * Método que valida si es un número entero entre 0 y 1
     * @param {string} - Número a validar
     * @return {bool}
     */
    function validar_limite_entero(string $numero): bool
    {
        return (trim($numero) < 0 || trim($numero) > 1) ? False : True;
    }

    /**
     * Método que valida si el texto tiene un formato válido de E-Mail
     * @param {string} - Email
     * @return {bool}
     */
    function validar_email(string $texto): bool
    {
        return (filter_var($texto, FILTER_VALIDATE_EMAIL) === FALSE) ? False : True;
    }

    // Variables que recogen los datos enviados en el formulario
    $errores = [];
    $username = isset($_POST['username']) ? $_POST['username'] : null;
    $email = isset($_POST['email']) ? $_POST['email'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;
    $enabled = isset($_POST['enabled']) ? $_POST['enabled'] : null;
    $isAdmin = isset($_POST['isAdmin']) ? $_POST['isAdmin'] : null;


    // Validaciones: Requerido, Formato Email y Number valido
    if (!validar_requerido($username)) {
        $errores[] = 'El campo username es obligatorio.';
    }

    if (!validar_requerido($email)) {
        $errores[] = 'El campo email es obligatorio.';
    }
    if (!validar_email($email)) {
        $errores[] = 'El campo email tiene un formato no válido.';
    }

    if (!validar_requerido($password)) {
        $errores[] = 'El campo password es obligatorio.';
    }
    if (!validar_requerido($enabled)) {
        $errores[] = 'El campo enabled es obligatorio.';
    }
    if (!validar_entero($enabled)) {
        $errores[] = 'El campo enabled debe ser un número.';
    }
    if (!validar_limite_entero($enabled)) {
        $errores[] = 'El campo enabled debe ser un número con valor 0-1.';
    }
    if (!(trim($isAdmin) == '')) {
        if (!validar_entero($isAdmin)) {
            $errores[] = 'El campo isAdmin debe ser un número.';
        }
        if (!validar_limite_entero($isAdmin)) {
            $errores[] = 'El campo isAdmin debe ser un número con valor 0-1.';
        }
    }

    if (empty($errores)) {

        $entityManager = Utils::getEntityManager();

        /** @var  $newUser */
        $newUser = new User($username, trim($email), trim($password), trim($enabled), trim($isAdmin));

        try {
            $entityManager->persist($newUser);
            $entityManager->flush();
            echo '<h3 style="color: #5042f4">' . 'Created User with ID #' . $newUser->getId() . '</h3>' . PHP_EOL;
        } catch (Exception $exception) {
            echo '<p style="color: #f45642;">' . $exception->getMessage() . '</p>'. PHP_EOL;
        }
    }


}
?>
<!-- Mostramos errores por HTML -->
<?php if (isset($errores)): ?>
    <ul style="color: #f45642;">
        <?php
        foreach ($errores as $error) {
            echo '<li>' . $error . '</li>';
        }
        ?>
    </ul>
<?php endif; ?>
<!-- Formulario -->
<form method="post">
    <div>
        <p><input type="text" name="username" placeholder="username"></p>
        <p><input type="text" name="email" placeholder="email"></p>
        <p><input type="text" name="password" placeholder="password"></p>
        <p><input type="text" name="enabled" placeholder="enabled"></p>
        <p><input type="text" name="isAdmin" placeholder="isAdmin"></p>
        <p><input type="submit" value="Crear"></p>
    </div>
</form>

</body>
</html>




