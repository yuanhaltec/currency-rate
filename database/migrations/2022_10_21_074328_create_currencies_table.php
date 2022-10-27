<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('currency', 100);
            $table->foreignId('created_by');
            $table->foreignId('updated_by');
            $table->timestamps();
        });

        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('from_id');
            $table->foreignId('to_id');
            $table->decimal('rate', 20, 10);
            $table->foreignId('created_by');
            $table->foreignId('updated_by');
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
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('currency_rates');
    }
}
