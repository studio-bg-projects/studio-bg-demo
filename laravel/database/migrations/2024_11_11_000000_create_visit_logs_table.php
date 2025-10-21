<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('visitLogs', function (Blueprint $table) {
      $table->id();
      $table->string('ipAddress', 45);
      $table->string('path');
      $table->string('requestMethod', 10);
      $table->string('referrer')->nullable();
      $table->text('userAgent')->nullable();
      $table->string('locale', 12)->nullable();
      $table->string('sessionId')->nullable();
      $table->timestamp('visitedAt')->useCurrent();
      $table->timestamp('createdAt')->useCurrent();
      $table->timestamp('updatedAt')->useCurrent()->useCurrentOnUpdate();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('visitLogs');
  }
};
