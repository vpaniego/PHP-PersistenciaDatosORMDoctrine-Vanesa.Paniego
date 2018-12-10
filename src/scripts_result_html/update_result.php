<!DOCTYPE html>
<html>
<body>
<h1>Formulario para la modificación de un resultado</h1>
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

    /**
     * Método que valida el formato de una fecha
     * @param {string} - Fecha a validar
     * @return {bool}
     */
    function validar_formato_fecha(string $time): bool
    {
        $valido = False;
        try {
            $date = new DateTime(trim($time));
            $date->format('Y-m-d H:i:sP');
            $valido = True;
        } catch (Exception $e) {
            $valido = False;
        }
        return $valido;
    }

    // Variables que recogen los datos enviados en el formulario
    $errores = [];
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $value = isset($_POST['value']) ? $_POST['value'] : null;
    $time = isset($_POST['time']) ? $_POST['time'] : null;
    $newTimestamp = new DateTime('now');

    // Validaciones: Requerido, Number válido y DateTime
    if (!validar_requerido($id)) {
        $errores[] = 'El campo id es obligatorio.';
    }

    if (!validar_entero($id)) {
        $errores[] = 'El campo id debe ser un número.';
    }

    if (!validar_entero_positivo($id)) {
        $errores[] = 'El campo id debe ser un número positivo mayor que 0.';
    }

    if (!validar_entero($value)) {
        $errores[] = 'El campo value debe ser un número.';
    }

    if (!(trim($time) == '')) {
        if (!validar_formato_fecha($time)) {
            $errores[] = 'El campo time no tiene el formato requerido (Y-m-d H:i:s).';
        } else {
            $newTimestamp = new DateTime(trim($time));
            $newTimestamp->format('Y-m-d H:i:sP');
        }
    }

    if (empty($errores)) {

        $entityManager = Utils::getEntityManager();

        /** Se comprueba que exista el result que se intenta modificar */
        /** @var Result $result */
        $resultId = (int)(trim($id));
        $result = $entityManager
            ->getRepository(Result::class)
            ->findOneBy(['id' => $resultId]);
        if (empty($result)) {
            echo '<h3 style="color: #f45642;">' . 'Result con ID #' . $resultId . ' no encontrado. No es posible modificar resultado inexistente.' . '</h3>' . PHP_EOL;
        } else {

            /** @var  $newResult */
            if (trim($value) !== '') {
                $resultValue = trim($value);
                $result->setResult((int)$resultValue);
            }
            if (!empty($newTimestamp)) {
                $result->setTime($newTimestamp);
            }

            try {
                $entityManager->persist($result);
                $entityManager->flush();
                echo '<h3 style="color: #5042f4">' . 'Actualizado Result with ID #' . $result->getId() . PHP_EOL;
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
        <p><input type="text" name="value" placeholder="value"></p>
        <p><input type="text" name="time" placeholder="time"></p>
        <p><input type="submit" value="Modificar"></p>
    </div>
</form>

</body>
</html>




