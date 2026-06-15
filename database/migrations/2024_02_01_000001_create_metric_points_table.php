<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metric_points', function (Blueprint $table) {
            $table->id();
            $table->string('account_id');
            $table->string('ad_id');
            $table->string('metric');
            $table->dateTime('recorded_at');
            $table->double('value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metric_points');
    }
};
