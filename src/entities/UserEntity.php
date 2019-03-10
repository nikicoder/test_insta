<?php

namespace instms\entities;

class UserEntity {

    protected $options = [
        'localId'       => null,
        'externalId'    => null, 
        'userName'      => null,
        'description'   => null
    ];

    /**
     * parseRawDataFromInstagram обрабатывает и присваивает данные 
     * полученные из API Instagram
     *
     * @param  mixed $rawData
     *
     * @return $this
     */
    public function parseRawDataFromInstagram($rawData)
    {
        // rawData may be object, need array
        settype($rawData, 'array');

        $this->options['externalId'] = $rawData['id'];
        $this->options['userName'] = $rawData['name'];
        $this->options['description'] = $rawData['info'];

        return $this;
    }

    public function parseRawDataFromDB($rawData)
    {
        settype($rawData, 'array');

        $this->options['localId'] = $rawData['id'];
        $this->options['externalId'] = $rawData['external_id'];
        $this->options['userName'] = $rawData['username'];
        $this->options['description'] = $rawData['description'];

        return $this;
    }


    public function __get($name) 
    {
        if(!array_key_exists($name, $this->options)) {
            throw new \Exception('Undefined user option.');
        }

        return $this->options[$name];
    }

    public function __set($name, $value) 
    {
        throw new \Exception('Setting properties directly prohibited.');
    }
}