<?php

namespace instms\entities;

class SubscriberEntity {

    protected $options = [
        'externalId'    => null, 
        'userName'      => null,
        'description'   => null,
        'state'         => false
    ];

    /**
     * parseRawDataFromInstagram 
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
        // инстаграм не может отдавать отписавшихся
        $this->options['state'] = true;

        return $this;
    }

    /**
     * parseRawDataFromDB
     *
     * @param  mixed $rawData
     *
     * @return $this
     */
    public function parseRawDataFromDB($rawData)
    {
        settype($rawData, 'array');

        $this->options['externalId'] = $rawData['external_id'];
        $this->options['userName'] = $rawData['username'];
        $this->options['state'] = (bool)$rawData['state'];

        return $this;
    }

    public function isActive(): bool
    {
        return $this->options['state'];
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