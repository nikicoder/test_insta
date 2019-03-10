<?php

use Phpmig\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddPostsLikesTable extends Migration
{
    const TABLE_NAME = 'user_posts_likes';
    
    public function init()
    {
        $this->schema = $this->get('db.schema');
    }

    /**
     * Do the migration
     */
    public function up()
    {
        $this->schema->create(self::TABLE_NAME, function (Blueprint $table) {
            $table->unsignedInteger('post_id')->index();
            $table->unsignedBigInteger('external_user_id');
            $table->foreign('post_id')->references('id')->on('user_posts');
        });
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        $this->schema->dropIfExists(self::TABLE_NAME);
    }
}
