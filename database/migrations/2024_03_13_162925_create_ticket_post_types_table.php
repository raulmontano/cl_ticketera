<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketPostTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_post_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('color');

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->integer('ticket_post_type_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_post_types');
    }
}
