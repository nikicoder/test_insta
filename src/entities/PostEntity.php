<?php

namespace instms\entities;

class PostEntity {

    protected $options = [
        'externalId'    => null, 
        'postType'      => null,
        'content'       => null,
        'likes'         => [],
        'views'         => 0
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
        $this->options['postType'] = $rawData['type'];
        $this->options['content'] = $rawData['content'];
        $this->options['likes'] = $rawData['likes'];
        
        if($rawData['type'] == 'video') {
            $this->options['views'] = $rawData['views'];
        }

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