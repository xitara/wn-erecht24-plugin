<?php namespace Xitara\DynamicContent\Updates;

use Schema;
use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;

class CreateERecht24TextsTable extends Migration
{
    public function up()
    {
        Schema::create('xitara_erecht24_texts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('lang', 5)->nullable();
            $table->mediumtext('text')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('xitara_erecht24_texts');
    }
}
