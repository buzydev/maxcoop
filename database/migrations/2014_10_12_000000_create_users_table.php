<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('referralUsername');
            $table->string('firstName');
            $table->string('lastName');
            $table->enum('plan', ['PLATINUM', 'GOLD', 'SILVER', 'BRONZE', 'INACTIVE'])->default('INACTIVE');
            $table->enum('role', ['ADMIN', 'MEMBER', 'SUPER_ADMIN'])->default('MEMBER');
            $table->string('avatar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('currentStep')->nullable()->default(0);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
