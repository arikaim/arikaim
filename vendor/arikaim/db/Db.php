<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Relations\Relation;

use PDOException;
use Exception;
use PDO;

/**
 * Manage database connections
*/
class Db  
{
    /**
     * Capsule manager object
     *
     * @var Illuminate\Database\Capsule\Manager
     */
    private $capsule;

    /**
     * Default PDO options
     *
     * @var array
     */
    protected $default_pdo_options = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    /**
     * Database config
     *
     * @var array
     */
    protected $config;

    /**
     * Constructor
     *
     * @param array $config
     * @param array|null $relations
     */
    public function __construct($config, $relations = null) 
    {
        $this->config = $config;
        $this->init($config); 

        // init relations morph map
        if (is_array($relations) == true) {                   
            Relation::morphMap($relations);                          
        }
    }

    /**
     * Get relations morph map
     *
     * @return array
     */
    public function getRelationsMap()
    {
        return Relation::$morphMap;
    }

    /**
     * Create db connection and boot Eloquent
     *
     * @param array $config
     * @return boolean
     */
    public function init($config)
    {
        try {              
            $this->capsule = new Manager();
            $this->capsule->addConnection($config);
            $this->capsule->setEventDispatcher(new \Illuminate\Events\Dispatcher());
            $this->capsule->setAsGlobal();
            // schema db             
            $this->initSchemaConnection($config);      
            $this->capsule->bootEloquent();
        } catch(Exception $e) {           
            return false;
        }      
      
        return true;
    }

    /**
     * Return capsule object
     *
     * @return object
     */
    public function getCapsule()
    {
        return $this->capsule;
    }

    /**
     *  Check if database exist
     *
     * @param string $databaseName
     * @return boolean
     */
    public function has($databaseName)
    {   
        try {
            $schema = $this->capsule->getConnection('schema');
            $result = $schema->select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$databaseName'");            
        } catch(Exception $e) {
            return false;
        }

        return (isset($result[0]->SCHEMA_NAME) == true) ? true : false;           
    }

    /**
     * Return true if conneciton is valid
     *
     * @param string|null $name
     * @return boolean
     */
    public function isValidConnection($name = null)
    {
        try {
            $connection = $this->capsule->getDatabaseManager()->connection($name);
            $pdo = $connection->getPdo(false);
        } catch(Exception $e) {
            return false;
        } catch(PDOException $e) {
            return false;
        } 
      
        return is_object($pdo);
    }

    /**
     * Return true if connection is valid
     *
     * @param array $config
     * @return boolean
     */
    public function isValidPdoConnection($config = null)
    {
        $config = ($config == null) ? $this->config : $config;
        $dsn = $config['driver'] . ":dbname=" .  $config['database'] . ";host=" . $config['host'];
    
        try {
            $pdo = new PDO($dsn,$config['username'],$config['password'],[
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        } catch(PDOException $e) {
            return false;
        }
       
        return is_object($pdo);
    }

    /**
     * Create database
     *
     * @param string $databaseName
     * @return boolean
     */
    public function createDb($databaseName, $charset = null, $collation = null) 
    {    
        if (Self::has($databaseName) == true) {
            return true;
        }

        $schema = $this->capsule->getConnection('schema');
        try {
            $charset = ($charset != null) ? "CHARACTER SET $charset" : "";
            $collation = ($charset != null) ? "COLLATE $collation" : "";

            $result = $schema->statement("CREATE DATABASE $databaseName $charset $collation");
        } catch(PDOException $e) {
            return false;
        }
        return $result;
    }

    /**
     * Verify db connection
     *
     * @param object $connection
     * @return boolean
     */
    public static function checkConnection($connection)
    {
        try {
            $result = $connection->statement('SELECT 1');
        } catch(PDOException $e) {
            return false;
        }
        return true;
    }

    /**
     * Test db connection
     *
     * @param array $config
     * @return bool
     */
    public function testConnection($config)
    {                
        try {
            $this->initSchemaConnection($config);     
            $this->capsule->getConnection('schema')->reconnect();
            $result = $this->checkConnection($this->capsule->getConnection('schema'));      
        } catch(PDOException $e) {   
            return false;
        }      
        return $result;
    }

    /**
     * Init db connection
     *
     * @param array $config
     * @return boolean
     */
    public function initConnection($config)
    {
        try {
            $this->capsule->addConnection($config,'new');
            $this->capsule->getDatabaseManager()->setDefaultConnection('new');
            $this->capsule->setAsGlobal();
        
            $this->initSchemaConnection($config);
            $this->capsule->getConnection('schema')->reconnect();

            $this->capsule->bootEloquent();
        } catch(PDOException $e) {   
            return false;
        }   

        return true;
    }

    /**
     * Add db schema conneciton
     *
     * @param array $config
     * @return void
     */
    private function initSchemaConnection($config)
    {
        $config['database'] = 'information_schema';             
        $this->capsule->addConnection($config,"schema");
    }

    /**
     * Get database info
     *
     * @return array
     */
    public function getInfo() 
    {        
        $pdo = (Self::hasPhpExtension('PDO') == true) ? $this->capsule->connection()->getPdo() : null;

        return [
            'driver'      => is_object($pdo) ? $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME) : "",
            'server_info' => is_object($pdo) ? $pdo->getAttribute(\PDO::ATTR_SERVER_INFO) : "",
            'version'     => is_object($pdo) ? substr($pdo->getAttribute(\PDO::ATTR_SERVER_VERSION),0,6) : "",
            'name'        => null
        ];      
    }
}
