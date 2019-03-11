<?php

namespace instms\models;

use \instms\entities\UserEntity;
use \instms\repositories\users\InstaLocalUsersRepository;
use \instms\repositories\users\InstaExternalUsersRepository;
use \instms\repositories\users\InstaLocalSubscribersRepository;

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
     * @param  string $nickname
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
     * getInstamSubscribers получает список инстаграм подписчиков из API
     *
     * @param  string $nickname
     *
     * @return array
     */
    public function getInstamSubscribers(string $nickname): array
    {
        return (new InstaExternalUsersRepository)->getSubscribersByNickname($nickname);
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

    /**
     * initUser данный метод инициализирует пользователя
     *
     * @param  mixed $user
     *
     * @return UserEntity
     */
    public function initUser(string $user): UserEntity
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

        $newUser = $this->getLocalUserByNickName($user);
        $subscribers = $this->getInstamSubscribers($newUser->userName);

        if(!empty($subscribers)) {
            (new InstaLocalSubscribersRepository)
                ->batchInsertSubscribers($newUser->localId, $subscribers);
        }

        return $newUser;
    }

    public function actualizeSubscribers(UserEntity $user)
    {
        $lsrp = new InstaLocalSubscribersRepository;
        
        // уже имеющиеся данные о подписчиках
        $currentSubscribersLocal = $lsrp->getAllSubscribers($user->localId);
        
        // Массив внешних ID активных аккаунтов
        $localActiveIDS = [];
        // Массив внешних ID неактивных аккаунтов
        $localInactive = [];

        foreach($currentSubscribersLocal as $acc) {
            if($acc->isActive()) {
                $localActiveIDS[] = $acc->externalId;
            } else {
                $localInactive[] = $acc->externalId;
            }
        }

        // актуальные данные из внешнего сервиса
        $externalSubsData = $this->getInstamSubscribers($user->userName);
        
        // добавить
        $add = [];
        // деактивировать
        $setInactive = [];
        // активировать по-новой
        $setActive = [];
        // обработанные
        $processed = [];

        foreach($externalSubsData as $eacc) {
            if(in_array($eacc->externalId, $localActiveIDS)) {
                // не делаем ничего
            } elseif(in_array($eacc->externalId, $localInactive)) {
                $setActive[] = $eacc;
            } else {
                $add[] = $eacc;
            }

            $processed[] = $eacc->externalId;
        }

        // те, кого нужно деактивировать
        foreach($localActiveIDS as $id) {
            if(!in_array($id, $processed)) {
                $setInactive[] = $id;
            }
        }

        // добавляем
        $lsrp->batchInsertSubscribers($user->localId, $add);

        // активируем обратно
        foreach($setActive as $u) {
            $lsrp->setActiveSubscriber($user->localId, $u->externalId);
        }

        // деактивируем
        foreach($setInactive as $id) {
            $lsrp->setInactiveSubscriber($user->localId, $id);
        }
    }
}