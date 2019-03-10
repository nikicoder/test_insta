<?php

namespace instms\repositories\users;

use \instms\repositories\BaseInstagramDataRepository;
use \instms\entities\UserEntity;

class InstaExternalUsersRepository extends BaseInstagramDataRepository {

    public function getUserByNickname(string $nickname): UserEntity
    {
        // это метод-заглушка, в реальном случае эти данные 
        // (пусть и не в таком виде) отдаст API инстаграм
        $rawData = [
            'id'    => $this->createPseudoUniqId($nickname),
            'name'  => $nickname,
            'info'  => ''
        ];

        return (new UserEntity)->parseRawDataFromInstagram($rawData);
    }

    // функция генерирует псевдоуникальный ID
    // для тестовых нужд
    // берем md5-hash от никнейма, дробим на 4 части
    // считаем от них число и суммируем все 4 части
    private function createPseudoUniqId(string $nickname)
    {
        $hash = md5($nickname);
        $p1 = intval(substr($hash, 0, 8), 16);
        $p2 = intval(substr($hash, 8, 8), 16);
        $p3 = intval(substr($hash, 16, 8), 16);
        $p4 = intval(substr($hash, 24, 8), 16);

        return ($p1 + $p2 + $p3 + $p4);
    }
}