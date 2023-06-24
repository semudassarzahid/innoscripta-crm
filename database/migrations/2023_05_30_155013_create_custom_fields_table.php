<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('custom_field_type_id');
            $table->boolean('required')->default(false);
            $table->integer('position');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->nullable()->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('custom_field_type_id')->references('id')->on('custom_field_types')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_fields', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
        Schema::dropIfExists('custom_fields');
    }
};
