<?php
/**
 * PHP version 7.2
 * src\Utils.php
 *
 * @category Utils
 * @package  MiW\Results
 * @author   Javier Gil <franciscojavier.gil@upm.es>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://www.etsisi.upm.es ETS de Ingeniería de Sistemas Informáticos
 */

namespace MiW\Results;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use MiW\Results\Entity\User;

/**
 * Trait Utils
 *
 * @package MiW\Results
 */
trait Utils
{

    /**
     * Genera el gestor de entidades
     *
     * @return EntityManager
     */
    public static function getEntityManager(): EntityManager
    {
        // Cargar configuración de la conexión
        $dbParams = array(
            'host'      => $_ENV['DATABASE_HOST'],
            'port'      => $_ENV['DATABASE_PORT'],
            'dbname'    => $_ENV['DATABASE_NAME'],
            'user'      => $_ENV['DATABASE_USER'],
            'password'  => $_ENV['DATABASE_PASSWD'],
            'driver'    => $_ENV['DATABASE_DRIVER'],
            'charset'   => $_ENV['DATABASE_CHARSET']
        );

        $config = Setup::createAnnotationMetadataConfiguration(
            array($_ENV['ENTITY_DIR']),    // paths to mapped entities
            $_ENV['DEBUG'],                // developper mode
            ini_get('sys_temp_dir'),       // Proxy dir
            null,                          // Cache implementation
            false                          // use Simple Annotation Reader
        );
        $config->setAutoGenerateProxyClasses(true);
        if ($_ENV['DEBUG']) {
            $config->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
        }

        $eManager = null;
        try {
            $eManager = EntityManager::create($dbParams, $config);
        } catch (ORMException $e) {
            echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
        }

        return $eManager;
    }

    /**
     * Get .env filename (.env.docker || .env || .env.dist)
     *
     * @param string $dir      directory
     * @param string $filename filename
     *
     * @return string
     */
    public static function getEnvFileName(
        string $dir = __DIR__,
        string $filename = '.env'
    ): string {

        if (isset($_ENV['docker'])) {
            return $filename . '.docker';
        } elseif (file_exists($dir . '/' . $filename)) {
            return $filename;
        } else {
            return $filename . '.dist';
        }
    }

    /**
     * Load user data fixtures
     *
     * @param string $username user name
     * @param string $email    user email
     * @param string $password user password
     * @param bool   $isAdmin  isAdmin
     *
     * @return void
     */
    public static function loadUserData(string $username, string $email,
        string $password, bool $isAdmin = false): void {
        $user = new User(
            $username,
            $email,
            $password,
            true,
            $isAdmin
        );
        $entityManager = self::getEntityManager();
        try {
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (ORMException $e) {
            echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
        }
    }

    /**
     * Drop & Update database schema
     *
     * @return void
     */
    public static function updateSchema(): void
    {
        $e_manager = self::getEntityManager();
        $metadata = $e_manager->getMetadataFactory()->getAllMetadata();
        $sch_tool = new SchemaTool($e_manager);
        $sch_tool->dropDatabase();
        $sch_tool->updateSchema($metadata, true);
    }
}
