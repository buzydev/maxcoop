<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('bankName')->nullable();
            $table->string('accountName')->nullable();
            $table->string('accountNumber')->nullable();
            //
            $table->string('userPlan')->nullable(); //
            $table->string('gender')->nullable();
            $table->string('dateOfBirth')->nullable();
            $table->string('relationshipStatus')->nullable();
            $table->string('address')->nullable();
            //
            $table->string('isHomeOwner')->nullable();
            $table->string('haveTakenMortgage')->nullable();
            $table->string('liveInARentedApartment')->nullable();
            $table->string('contributeToNHF')->nullable();
            $table->string('benefittedFromNHF')->nullable();
            //
            $table->string('numberOfDependant')->nullable();
            $table->string('careerType')->nullable();
            $table->string('occupation')->nullable();
            $table->string('industry')->nullable();
            $table->string('grossAnnualIncome')->nullable();
            $table->string('netMonthlyIncome')->nullable();
            $table->string('incomeSource')->nullable();
            $table->string('employer')->nullable();
            $table->string('jobStatus')->nullable();
            $table->string('employmentStatus')->nullable();
            //
            $table->string('educationalLevel')->nullable();
            $table->string('nextOfKin')->nullable();
            $table->string('nextOfKinRelationship')->nullable();
            $table->string('nextOfKinPhone')->nullable();
            $table->string('emergencyContactName')->nullable();
            $table->string('emergencyContactPhone')->nullable();
            //
            $table->string('passport')->nullable();
            $table->string('validId')->nullable();
            $table->string('utilityBill')->nullable();
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
        Schema::dropIfExists('account_details');
    }
}
