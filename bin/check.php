#!/usr/bin/env php
<?php

// add namespaces support
require_once __DIR__ . '/../vendor/autoload.php';

use \instms\models\UsersModel;
use \instms\models\PostsModel;

// parse arguments
$shortopts  = "h";
$longopts  = [
    "user:",
    "posts_num:",
];
$options = getopt($shortopts, $longopts);

if(array_key_exists('h', $options) || empty($options)) {
    // Выводим справку и завершаем работу
    printHelp();
    exit();
}

$user = (string)$options['user'];
$postsNum = (int)$options['posts_num'];

$userModel = new UsersModel;
$postsModel = new PostsModel;

try {
    // 1. Проверка существует такой пользователь или нет
    $userData = $userModel->getLocalUserByNickName($user);

    if(empty($userData)) {
        // если пользователь отсутствует инициализируем его
        $userData = $userModel->initUser($user);
    } else {
        // если пользователь уже был инициализирован -- актуализируем
        // состояние подписчиков
        $userModel->actualizeSubscribers($userData);
    }

    // 2. Сканирование новых постов
    $newPosts = $postsModel->checkNewPosts($userData);

    // 3. Обновление данных по последним $postsNum
    $postsModel->updateLastPosts($userData, $postsNum, $newPosts);

} catch(\Exeption $e) {
    // тут необходимо использовать фасад логирования ошибок
    // но его реализация выходит за рамки задания
}

function printHelp() 
{
    echo "\n Usage: php check.php --user=@someuser --posts_num=30\n\n";
}