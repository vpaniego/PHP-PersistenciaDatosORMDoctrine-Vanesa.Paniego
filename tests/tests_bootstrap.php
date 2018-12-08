<?php
/**
 * PHP version 7.2
 * test/test_bootstrap.php
 *
 * @category TestFuncional
 * @author   Javier Gil <franciscojavier.gil@upm.es>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://www.etsisi.upm.es ETS de Ingeniería de Sistemas Informáticos
 */

use MiW\Results\Utils;

require_once __DIR__ . '/../vendor/autoload.php';

mt_srand();

// Create/update tables in the test database
Utils::updateSchema();
