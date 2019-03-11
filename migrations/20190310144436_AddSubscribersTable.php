<?php

use Phpmig\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddSubscribersTable extends Migration
{
    const TABLE_NAME = 'user_subscribers';

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
            $table->unsignedInteger('user_id')->index();
            $table->string('username')->index();
            $table->unsignedBigInteger('external_id')->index();
            $table->timestamp('added');
            $table->timestamp('updated')->nullable();
            $table->boolean('state');
            $table->foreign('user_id')->references('id')->on('instagram_users');
            $table->unique(['user_id', 'external_id']);
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
