<?php

namespace instms\repositories\posts;

use \instms\repositories\BaseDBRepository;
use \instms\entities\PostEntity;

class InstaLocalPostsRepository extends BaseDBRepository {

    const TABLE_NAME = 'user_posts';
    const PRIMARY_KEY_FIELD = 'id';

    /**
     * getLastPost получить последний сохраненный пост аккаунта
     *
     * @param  int $userId
     *
     * @return array
     */
    public function getLastPost(int $userId): array
    {
        return (array)$this->db->table(self::TABLE_NAME)
            ->where('user_id', '=', $userId)
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * getLastPosts получить последние посты
     *
     * @param  int $userId
     * @param  int $postsNum
     *
     * @return array
     */
    public function getLastPosts(int $userId, int $postsNum): array
    {
        return (array)$this->db->table(self::TABLE_NAME)
            ->where('user_id', '=', $userId)
            ->orderBy('id', 'desc')
            ->limit($postsNum)
            ->get();
    }

    /**
     * updateViews обновить количество просмотров
     *
     * @param  int $postId
     * @param  int $count
     *
     * @return void
     */
    public function updateViews(int $postId, int $count)
    {
        return $this->db->table(self::TABLE_NAME)
            ->where('id', '=', $postId)
            ->update(['views' => $count]);
    }

    /**
     * addPost добавить пост к отслеживаемым
     *
     * @param  mixed $userId
     * @param  mixed $post
     *
     * @return void
     */
    public function addPost(int $userId, PostEntity $post)
    {
        $ts = (new \DateTime)->format('Y-m-d H:i:s');

        $data = [
            'user_id'           => $userId,
            'external_post_id'  => $post->externalId,
            'post_type'         => $post->postType,
            'views'             => $post->views,
            'added'             => $ts,
            'updated'           => $ts,
            'state'             => 1
        ];

        return (int)$this->insertRData($data);
    }
}