<?php

namespace instms\repositories\users;

use \instms\repositories\BaseInstagramDataRepository;
use \instms\entities\UserEntity;
use \instms\entities\SubscriberEntity;

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

    public function getSubscribersByNickname(string $nickname): array
    {
        $result = [];

        foreach($this->users as $u) {
            // эмуляция подписался-отписался
            if(rand(0,1)) {
                $result[] = (new SubscriberEntity)->parseRawDataFromInstagram([
                    'id'    => $this->createPseudoUniqId($u),
                    'name'  => $u,
                    'info'  => ''
                ]);
            }
        }

        return $result;
    }
}