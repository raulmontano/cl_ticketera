<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsTicketEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_events', function (Blueprint $table) {
            $table->unsignedInteger('assigned_to_user_id')->nullable()->after('user_id');
            $table->unsignedInteger('assigned_to_team_id')->nullable()->after('assigned_to_user_id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('ticket_events', function (Blueprint $table) {
          $table->dropColumn('assigned_to_user_id');
          $table->dropColumn('assigned_to_team_id');
      });
    }
}
