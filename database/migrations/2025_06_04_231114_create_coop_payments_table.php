<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoopPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coop_payments', function (Blueprint $table) {
            $table->id();
            $table->string('imageUrl');
            $table->string('paymentType');
            $table->string('paymentDate');
            $table->enum('status', ['PENDING', 'SUCCESS', 'REJECTED'])->default('PENDING');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('coop_payments');
    }
}
