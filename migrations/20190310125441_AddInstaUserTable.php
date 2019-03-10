<?php

use Phpmig\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddInstaUserTable extends Migration
{
    const TABLE_NAME = 'instagram_users';

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
            $table->unsignedBigInteger('external_id');
            $table->string('username')->index();
            $table->text('description')->nullable();
            $table->timestamp('created')->nullable();
            $table->boolean('state');
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
