<?php

namespace instms\repositories\users;

use \instms\repositories\BaseDBRepository;
use \instms\entities\SubscriberEntity;

class InstaLocalSubscribersRepository extends BaseDBRepository {

    const TABLE_NAME = 'user_subscribers';
    const PRIMARY_KEY_FIELD = null;

    
    /**
     * getAllSubscribers возвращает весь список (включая отписавщихся)
     * подписчиков аккаунта
     *
     * @return array
     */
    public function getAllSubscribers(int $userId): array
    {
        $result = [];
        $raw = $this->db->table(self::TABLE_NAME)
            ->where('user_id', '=', $userId)
            ->get();

        foreach($raw as $r) {
            $result[] = (new SubscriberEntity)->parseRawDataFromDB($r);
        }

        return $result;
    }

    /**
     * setActiveSubscriber если подписчик был ранее был подписан и отписался
     *
     * @param  mixed $userId
     * @param  mixed $extId
     *
     * @return void
     */
    public function setActiveSubscriber(int $userId, int $extId)
    {
        return $this->db->table(self::TABLE_NAME)
            ->where('user_id', '=', $userId)
            ->where('external_id', '=', $extId)
            ->update(['state' => 1]);
    }

    /**
     * setInactiveSubscriber если подписчик отписался от аккаунта
     *
     * @param  mixed $userId
     * @param  mixed $extId
     *
     * @return void
     */
    public function setInactiveSubscriber(int $userId, int $extId)
    {
        return $this->db->table(self::TABLE_NAME)
            ->where('user_id', '=', $userId)
            ->where('external_id', '=', $extId)
            ->update(['state' => 0]);
    }

    /**
     * batchInsertSubscribers массовое добавление подписчиков
     * 
     * Метод оптимизирован для пакетного добавления подписчиков
     * рекомендуется к использованию как более быстрая альтернатива
     * одиночному добавлению подписчика. При использовании метода
     * необходимо заранее убедиться что все добавляемые подписчики для данного
     * $userId новые, метод данную ситуацию не контролирует
     *
     * @param  mixed $users
     *
     * @return void
     */
    public function batchInsertSubscribers(int $userId, array $subscribers)
    {
        $pdo = $this->db->connection()->getPdo();

        $now = (new \DateTime)->format('Y-m-d H:i:s');
        $state = 1;

        $stmt = $pdo->prepare("INSERT INTO user_subscribers 
            (user_id, username, external_id, added, updated, state) VALUES 
            (:user_id, :username, :external_id, :added, :updated, :state)");
        
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':added', $now);
        $stmt->bindParam(':updated', $now);
        $stmt->bindParam(':state', $state);

        foreach($subscribers as $subs) {
            // чтобы не лезла ошибка уровня E_NOTICE
            $username = $subs->userName;
            $extId = $subs->externalId;

            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':external_id', $extId);
            $stmt->execute();
        }

        unset($stmt);
        unset($pdo);
    }
}