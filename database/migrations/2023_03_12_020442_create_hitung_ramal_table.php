<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHitungRamalTable extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hitung_ramal', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('periode');
            $table->unsignedBigInteger('bullwhip_effect_id');
            $table->integer('id_kategori');
            $table->bigInteger('jumlah_jual');
            $table->bigInteger('jumlah');
            $table->timestamps();

            $table->foreign('bullwhip_effect_id', 'hitung_ramal_bullwhip_effect_id_foreign')
                ->references('id')
                ->on('bullwhip_effects2')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hitung_ramal');
    }
}
