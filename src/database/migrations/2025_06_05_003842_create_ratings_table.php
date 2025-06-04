<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rater_id')->constrained('users')->onDelete('cascade'); // 評価したユーザー
            $table->foreignId('ratee_id')->constrained('users')->onDelete('cascade'); // 評価されたユーザー
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade'); // 評価対象の商品
            $table->enum('role', ['seller', 'buyer']); // 出品者評価 or 購入者評価
            $table->tinyInteger('rating'); // 1～5段階の整数値
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
        Schema::dropIfExists('ratings');
    }
}
