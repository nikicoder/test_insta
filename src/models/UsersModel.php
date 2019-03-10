<?php

namespace instms\models;

use \instms\entities\UserEntity;
use \instms\repositories\users\InstaLocalUsersRepository;
use \instms\repositories\users\InstaExternalUsersRepository;

class UsersModel {

    /**
     * getLocalUserByNickName получение данных пользователя из локального источника
     *
     * @param  string $nickname
     *
     * @return mixed
     */
    public function getLocalUserByNickName(string $nickname)
    {
        $userdata = (new InstaLocalUsersRepository)
            ->getUserByNickname($nickname);

        if(empty($userdata)) {
            return null;
        }

        return (new UserEntity)->parseRawDataFromDB($userdata);
    }

    /**
     * getInstagramUserByNickname получение данных из внешнего источника
     *
     * @param  mixed $nickname
     *
     * @return mixed
     */
    public function getInstagramUserByNickname(string $nickname)
    {
        // обработка случаев когда инстаграм не возвращает данные
        // не входит в регламент текущего задания
        // по этому инстаграм апи какбы всегда что-то возвращает
        return (new InstaExternalUsersRepository)->getUserByNickname($nickname);
    }

    /**
     * addLocalUser добавление пользователя локально для последующей работы с ним
     *
     * @param  mixed $user
     *
     * @return void
     */
    public function addLocalUser(UserEntity $user)
    {
        return (new InstaLocalUsersRepository)->addUser($user);
    }

    public function initUser(string $user)
    {
        // тип у getInstagramUserByNickname mixed
        // по этому проверка на пустое значение нужна by design
        $userData = $this->getInstagramUserByNickname($user);
        if(empty($userData)) {
            throw new \Exception('Instagram user not exists');
        }

        $id = $this->addLocalUser($userData);
        if($id <= 0) {
            throw new \Exception('Error save user local');
        }

        return $this->getLocalUserByNickName($user);
    }
}