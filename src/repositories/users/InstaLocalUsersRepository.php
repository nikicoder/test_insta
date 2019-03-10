<?php

namespace instms\repositories\users;

use \instms\repositories\BaseDBRepository;
use \instms\entities\UserEntity;

class InstaLocalUsersRepository extends BaseDBRepository {

    const TABLE_NAME = 'instagram_users';
    const PRIMARY_KEY_FIELD = 'id';

    /**
     * getUserByNickname получение отслеживаемого пользователя по ID
     * 
     * Пользователь должен быть активным, во всех остальных случаях
     * предпочтительно использовать общий метод getRDataByID
     *
     * @param  string $nickname
     *
     * @return array
     */
    public function getUserByNickname(string $nickname): array
    {
        return (array)$this->db->table(self::TABLE_NAME)
            ->where('username', '=', $nickname)
            ->where('state', '=', 1)
            ->first();
    }

    /**
     * addUser записывает в БД пользователя
     *
     * @param  UserEntity $user
     *
     * @return int
     */
    public function addUser(UserEntity $user): int
    {
        $data = [
            'external_id'   => $user->externalId,
            'username'      => $user->userName,
            'description'   => $user->description,
            'created'       => new \DateTime,
            'state'         => 1
        ];

        // Валидация данных должна быть тут
        // но она выходит за рамки тестового задания

        return (int)$this->insertRData($data);
    }
}