<?php

namespace instms\repositories;

use Illuminate\Database\Capsule\Manager as Capsule;
use Pimple\Container;
use instms\config\Settings;

/*
    Родительский класс для получения данных из БД
    
    Данный класс должен наследоваться всеми репозиториями
    которые получают данные из источника данны БД

    Класс содержит в себе CRU методы, специфичные методы
    реализуются в дочерних классах
*/

abstract class BaseDBRepository
{
    protected $db;

    public function __construct()
    {
        global $container;

        // Для инициализации БД нужен контейнер зависимостей
        if(!($container instanceof ContainerInterface)) {
            $container = new Container;
        }

        // Если БД не была инициализирована прежде
        if(empty($container['db'])) {
            $settings = new Settings;

            $container['config'] = [
                'driver'    => $settings->dbDriver,
                'host'      => $settings->dbHost,
                'port'      => $settings->dbPort,
                'database'  => $settings->dbName,
                'username'  => $settings->dbUser,
                'password'  => $settings->dbPassword,
                'charset'   => $settings->dbCharset,
                'collation' => $settings->dbCollation,
                'prefix'    => $settings->dbPrefix,
            ];
            
            $container['db'] = function ($c) {
                $capsule = new Capsule();
                $capsule->addConnection($c['config']);
                $capsule->setAsGlobal();
                $capsule->bootEloquent();
            
               return $capsule;
            };
        }

        $this->db =& $container['db'];
    }

    /**
     * getRDataByID функция возвращает данные из источника по ID
     * ID в данном случае является первичным ключем
     *
     * @param  $id
     *
     * @return array
     */

    public function getRDataByID($id): array
    {
        if(empty(static::PRIMARY_KEY_FIELD)) {
            throw new \Exception('Repository not supported getRDataByID() method');
        }

        return (array)$this->db->table(static::TABLE_NAME)
            ->where(static::PRIMARY_KEY_FIELD, '=', $id)
            ->first();
    }

    /**
     * insertRData функция добавляет данные в источник
     *
     * @param  array $data
     * 
     * @return mixed
     */
    protected function insertRData(array $data)
    {
        return !empty(static::PRIMARY_KEY_FIELD) ? 
            $this->db->table(static::TABLE_NAME)
                ->insertGetId($data) :
            $this->db->table(static::TABLE_NAME)
                ->insert($data);
    }

    /**
     * updateRDataByID функция обновляет данные в источнике по ID
     *
     * @param  int $id
     *
     * @return mixed
     */
    protected function updateRDataByID(int $id, $updateData)
    {
        return $this->db->table(static::TABLE_NAME)
            ->where(static::PRIMARY_KEY_FIELD, '=', $id)
            ->update($updateData);
    }
}