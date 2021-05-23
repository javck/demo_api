<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->bigInteger('category_id')->unsigned()->index(); //unsigned() 非負數，index() 加入索引
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->text('content');
            $table->string('pic', 255);
            $table->integer('sort')->default(0);
            $table->boolean('enabled')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //移除外鍵關係
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });
        Schema::dropIfExists('posts');
    }
}
