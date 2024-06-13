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
        Schema::create('inboud_stuff', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("stuff_id");
            $table->integer("total");
            $table->date("date");
            $table->string("proff_file");
            $table->timestamps();
            $table->softdeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inboud_stuff');
    }
};
