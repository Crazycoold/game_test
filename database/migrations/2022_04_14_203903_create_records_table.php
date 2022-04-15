<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('game_id');
            $table->foreign('game_id')->references('id')->on('games')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->unsignedBigInteger('round_id');
            $table->foreign('round_id')->references('id')->on('rounds')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->unsignedTinyInteger('player_type');
            $table->unsignedSmallInteger('game_row');
            $table->unsignedSmallInteger('game_column');
            $table->unique([
                'round_id',
                'player_type',
                'game_row',
                'game_column',
            ]);
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
        Schema::dropIfExists('records');
    }
}
