<?php

namespace instms\repositories;

/*
    Базовый класс для получения данных с инстаграм

    Предполагается что он содержит методы для формирования запросов к АПИ
    методы работы с токенами и т.д.

*/

abstract class BaseInstagramDataRepository {

    protected $users = [
        '@masha', '@ivan', '@nogotochki', '@coffe_vsem', '@dima777',
        '@svetik89', '@alexey.infinity', '@stepka', '@seo_smm',
        '@auto_salon', '@vikrorya', '@elena', '@kirill', '@feofan',
        '@catlovers', '@hyena', '@barbershop', '@fitness_sport'
    ];

    // эмуляция данных какбэ постов
    protected function generatePosts(int $postsNum)
    {
        $result = [];
        $ts = time();

        for($i = 0; $i < $postsNum; $i++) {
            $post = [
                'id'        => $ts - $i*10, // эмуляция DESC
                'type'      => rand(0,1) ? 'video' : 'image',
                'content'   => ''
            ];

            if($post['type'] == 'video') {
                $post['views'] = rand(10,999);
            }

            $post['likes'] = [];

            $likesCount = rand(1, count($this->users) - 1);
            
            while($likesCount > count($post['likes'])) {
                $idx = rand(0, count($this->users) - 1);
                $post['likes'][] = $this->createPseudoUniqId($this->users[$idx]);
                $post['likes'] = array_unique($post['likes']);
            }

            $result[] = $post;
        }

        return $result;
    }

    protected function generateNewPost($lastId)
    {
        $post = [
            'id'        => $lastId + 10,
            'type'      => rand(0,1) ? 'video' : 'image',
            'content'   => ''
        ];

        if($post['type'] == 'video') {
            $post['views'] = rand(10,999);
        }

        $post['likes'] = [];

        $likesCount = rand(1, count($this->users) - 1); 
        while($likesCount > count($post['likes'])) {
            $idx = rand(0, count($this->users) - 1);
            $post['likes'][] = $this->createPseudoUniqId($this->users[$idx]);
            $post['likes'] = array_unique($post['likes']);
        }

        return $post;
    }

    // функция генерирует псевдоуникальный ID
    // для тестовых нужд
    // берем md5-hash от никнейма, дробим на 4 части
    // считаем от них число и суммируем все 4 части
    // ID повторяемый, т.е. другой функцией в случае необходимости
    // из этой же строки сгенерируется такой же ID

    protected function createPseudoUniqId(string $nickname)
    {
        $hash = md5($nickname);
        $p1 = intval(substr($hash, 0, 8), 16);
        $p2 = intval(substr($hash, 8, 8), 16);
        $p3 = intval(substr($hash, 16, 8), 16);
        $p4 = intval(substr($hash, 24, 8), 16);



        return ($p1 + $p2 + $p3 + $p4);
    }
}