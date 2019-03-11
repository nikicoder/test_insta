<?php

namespace instms\repositories\posts;

use \instms\repositories\BaseDBRepository;

class InstaLocalPostLikesRepository extends BaseDBRepository {

    const TABLE_NAME = 'user_posts_likes';
    const PRIMARY_KEY_FIELD = null;

    /**
     * updateLikesBatch
     *
     * @param  int $postId
     * @param  array $likes
     *
     * @return void
     */
    public function updateLikesBatch(int $postId, array $likes)
    {
        $pdo = $this->db->connection()->getPdo();

        $stmt = $pdo->prepare("INSERT IGNORE INTO user_posts_likes 
            (post_id, external_user_id) VALUES 
            (:post_id, :external_user_id)");
        
        $stmt->bindParam(':post_id', $postId);

        foreach($likes as $likedUsedId) {

            $stmt->bindParam(':external_user_id', $likedUsedId);
            $stmt->execute();
        }

        unset($stmt);
        unset($pdo);

        // подчищаем снятые лайки
        $this->db->table(self::TABLE_NAME)
            ->where('post_id', '=', $postId)
            ->whereNotIn('external_user_id', $likes)
            ->delete();
    }
}