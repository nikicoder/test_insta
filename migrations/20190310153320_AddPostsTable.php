<?php

use Phpmig\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddPostsTable extends Migration
{
    const TABLE_NAME = 'user_posts';
    
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
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->unsignedBigInteger('external_post_id')->index();
            $table->string('post_type');
            $table->integer('views')->nullable();
            $table->timestamp('added');
            $table->timestamp('updated')->nullable();
            $table->boolean('state');
            $table->foreign('user_id')->references('id')->on('instagram_users');
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
