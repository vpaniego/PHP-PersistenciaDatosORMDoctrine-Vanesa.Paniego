<!DOCTYPE html>
<html>
<body>
<h1>Formulario para la baja de resultados</h1>
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
     * Método que valida si es un número entero positivo mayor que 0
     * @param {string} - Número a validar
     * @return {bool}
     */
    function validar_entero_positivo(string $numero): bool
    {
        return (trim($numero) <= 0) ? False : True;
    }

// Variables que recogen los datos enviados en el formulario
    $errores = [];
    $resultId = isset($_POST['id']) ? $_POST['id'] : null;

// Validaciones: Requerido y Number válido
    if (!validar_requerido($resultId)) {
        $errores[] = 'El campo id es obligatorio.';
    }

    if (!validar_entero($resultId)) {
        $errores[] = 'El campo id debe ser un número.';
    }

    if (!validar_entero_positivo($resultId)) {
        $errores[] = 'El campo id debe ser un número positivo mayor que 0.';
    }

    if (empty($errores)) {

        $entityManager = Utils::getEntityManager();
        $id = (int)(trim($resultId));
        $result = $entityManager
            ->getRepository(Result::class)
            ->findOneBy(['id' => $id]);
        if (empty($result)) {
            echo '<h3 style="color: #f45642;">' . 'Result con ID #' . $id . ' no existe en la base de datos.' . '</h3>' . PHP_EOL;
        } else {
            /** @var  $result */
            try {
                $entityManager->remove($result);
                $entityManager->flush();
                echo '<h3 style="color: #5042f4">' . 'Eliminado Result con ID #' . $id . '</h3>' . PHP_EOL;
            } catch (Exception $exception) {
                echo '<p style="color: #f45642;">' . $exception->getMessage() . '</p>' . PHP_EOL;
            }
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
        <p><input type="text" name="id" placeholder="id"></p>
        <p><input type="submit" value="Eliminar"></p>
    </div>
</form>

</body>
</html>
