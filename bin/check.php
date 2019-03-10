#!/usr/bin/env php
<?php

// add namespaces support
require_once __DIR__ . '/../vendor/autoload.php';

use \instms\models\UsersModel;

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
$posts = (int)$options['posts_num'];

$userModel = new UsersModel;

// 1. Проверка существует такой пользователь или нет
// если пользователь отсутствует инициализируем его
$userData = $userModel->getLocalUserByNickName($user);

if(empty($userData)) {
    $userData = $userModel->initUser($user);
}

function printHelp() 
{
    echo "\n Usage: php check.php --user=@someuser --posts_num=30\n\n";
}