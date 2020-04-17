<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        //Schema::create('users', function (Blueprint $table) {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_email_unique');
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();

            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->string('avatar')->nullable();
            $table->text('token')->nullable();
            $table->string('refresh_token')->nullable();
            $table->string('token_type')->nullable();
            $table->string('expires_in')->nullable();
            $table->string('expires_on')->nullable();
            $table->string('character_owner_hash')->nullable();
            $table->string('scopes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable(false)->change();
            $table->string('email')->unique()->change();
            $table->string('email')->nullable(false)->change();

            $table->dropColumn('provider');
            $table->dropColumn('provider_id');
            $table->dropColumn('avatar');
            $table->dropColumn('token');
            $table->dropColumn('refresh_token');
            $table->dropColumn('token_type');
            $table->dropColumn('expires_in');
            $table->dropColumn('expires_on');
            $table->dropColumn('character_owner_hash');
            $table->dropColumn('scopes');
        });

        // usertable will be deleted by original migrations
        //Schema::dropIfExists('users');
    }
}
