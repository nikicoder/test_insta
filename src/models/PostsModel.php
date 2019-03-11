<?php

namespace instms\models;

use \instms\entities\UserEntity;
use \instms\repositories\posts\InstaLocalPostsRepository;
use \instms\repositories\posts\InstaExternalPostsRepository;
use \instms\repositories\posts\InstaLocalPostLikesRepository;

class PostsModel {

    /**
     * checkNewPosts проверка новых постов и добавление их в локальный источник
     *
     * @param  UserEntity $user
     *
     * @return void
     */
    public function checkNewPosts(UserEntity $user)
    {
        $newPosts = [];
        
        $prp = new InstaLocalPostsRepository;
        $plrp = new InstaLocalPostLikesRepository;

        // получаем внещний ID последнего поста
        $lastPost = $prp->getLastPost($user->localId);
        if(empty($lastPost)) {
            $posts = (new InstaExternalPostsRepository)
                ->getAllPostsByNickname($user->userName);
        } else {
            // здесь удобнее без промежуточного объекта
            $posts = (new InstaExternalPostsRepository)
                ->getLastPostsByNickname($user->userName, $lastPost['external_post_id']);
        }

        if(!empty($posts)) {
            foreach($posts as $p) {
                $postId = $prp->addPost($user->localId, $p);
                $plrp->updateLikesBatch($postId, $p->likes);
                $newPosts[] = $postId;
            }
        }

        return $newPosts;
    }

    /**
     * updateLastPosts обновление последних постов
     *
     * @param  UserEntity $user
     * @param  int $lastPosts
     * @param  array $skipPosts
     *
     * @return void
     */
    public function updateLastPosts(UserEntity $user, int $lastPosts, array $skipPosts = [])
    {
        $prp = new InstaLocalPostsRepository;
        $plrp = new InstaLocalPostLikesRepository;
        $extprp = new InstaExternalPostsRepository;

        $postsToUpdate = [];
        $lastPosts = $prp->getLastPosts($user->localId, $lastPosts);

        foreach($lastPosts as $p) {
            settype($p, 'array'); // потому что работа с сырыми данными из источника
            if(!in_array($p['external_post_id'], $skipPosts)) {
                $postsToUpdate[] = [
                    'local_id'  => $p['id'],
                    'ext_id'    => $p['external_post_id'],
                    'type'      => $p['post_type']
                ];
            }
        }

        // вообще в реальной задаче я бы скорее взял последние либо все посты
        // либо посты > наименьшего ID, и из них достал нужныено в своем мок-объекте 
        // нет сохранения состояния у внешнего источника, по этому реализация в данном случае
        // не такая какую бы сделал на реальной задаче, а такая какая корректно отработает сейчас
        // а вообще дергать посты поштучно это самое узкое место из-за сетевого оверхеда
        foreach($postsToUpdate as $p) {
            $extPostData = $extprp->getPostById($p['ext_id']);
            if($p['type'] == 'video') {
                $prp->updateViews($p['local_id'], $extPostData->views);
            }
            $plrp->updateLikesBatch($p['local_id'], $extPostData->likes);
        }
    }
}