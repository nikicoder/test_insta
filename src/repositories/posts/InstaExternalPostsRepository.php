<?php

namespace instms\repositories\posts;

use \instms\repositories\BaseInstagramDataRepository;
use \instms\entities\PostEntity;
use \instms\entities\SubscriberEntity;

class InstaExternalPostsRepository extends BaseInstagramDataRepository {

    public function getAllPostsByNickname(string $nickname)
    {
        $result = [];

        // $nickname не используется, но по задумке запрос по нему
        $posts = $this->generatePosts(rand(10, 99));
        foreach($posts as $p) {
            $result[] = (new PostEntity)->parseRawDataFromInstagram($p);
        }

        return $result;
    }

    public function getLastPostsByNickname(string $nickname, int $lastPostId)
    {
        $result = [];

        // $nickname не используется, но по задумке запрос по нему аналогично
        // в заглушке всегда будет 1 или 0 новых постов
        if(rand(0,1)) {
            $post = $this->generateNewPost($lastPostId);
            $result[] = (new PostEntity)->parseRawDataFromInstagram($post);
        }

        return $result;
    }

    public function getPostById(int $postId)
    {
        $post = [
            'id'        => $postId,
            'type'      => null, // это все равно будет проигнорировано
            'content'   => ''
        ];

        // это тут будет безусловно, а в модели от того какой был тип в базе
        $post['views'] = rand(10,999);

        $post['likes'] = [];

        $likesCount = rand(1, count($this->users) - 1); 
        while($likesCount > count($post['likes'])) {
            $idx = rand(0, count($this->users) - 1);
            $post['likes'][] = $this->createPseudoUniqId($this->users[$idx]);
            $post['likes'] = array_unique($post['likes']);
        }

        return (new PostEntity)->parseRawDataFromInstagram($post);
    }
}