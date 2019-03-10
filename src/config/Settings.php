<?php
namespace instms\config;

use josegonzalez\Dotenv\Loader;

class Settings {

    // map file options to class options
    const MAP_OPTIONS = [
        'DB_DRIVER'     => 'dbDriver',
        'DB_HOST'       => 'dbHost',
        'DB_PORT'       => 'dbPort',
        'DB_USER'       => 'dbUser',
        'DB_PASSWORD'   => 'dbPassword',
        'DB_NAME'       => 'dbName',
        'DB_CHARSET'    => 'dbCharset',
        'DB_COLLATION'  => 'dbCollation',
        'DB_PREFIX'     => 'dbPrefix'
    ];

    // available options and defaults
    protected $options = [
        'dbDriver'      => 'mysql', 
        'dbHost'        => '',
        'dbPort'        => 3306,
        'dbUser'        => '',
        'dbPassword'    => '',
        'dbName'        => '',
        'dbCharset'     => 'utf8mb4',
        'dbCollation'   => 'utf8mb4_unicode_ci',
        'dbPrefix'      => '', 
    ];

    public function __construct()
    {
        $options = $this->parseENVFile();
        
        $this->mapOptions($options);
    }

    public function __get($name) 
    {
        if(!array_key_exists($name, $this->options)) {
            throw new \Exception('Undefined settings option.');
        }

        return $this->options[$name];
    }

    public function __set($name, $value) 
    {
        throw new \Exception('Setting properties directly prohibited.');
    }

    /**
     * mapOptions mapping env file options
     *
     * @param  mixed $rawEnvData
     *
     * @return void
     */
    private function mapOptions(array $rawEnvData)
    {
        foreach(self::MAP_OPTIONS as $envName => $mapName) {
            if(!empty($rawEnvData[$envName])) {
                $this->options[$mapName] = $rawEnvData[$envName];
            }
        }
    }

    /**
     * parseENVFile parse .env file options
     *
     * @throws \Exception
     * @return array
     */
    private function parseENVFile(): array
    {
        $envPath = __DIR__ . DIRECTORY_SEPARATOR . '..'
             . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '.env';

        if(!is_file($envPath)) {
            throw new \Exception(".env file not found");
        }

        $options = (new Loader($envPath))
              ->parse()
              ->toArray();
        
        return (array)$options;
    }
}