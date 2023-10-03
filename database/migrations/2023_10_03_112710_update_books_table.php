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
    Schema::table('books', function (Blueprint $table) {
      $table->string('blurb')->nullable();
      $table->string('claimed_by_name')->nullable();
      $table->integer('page_count')->nullable();
      $table->integer('year')->nullable();
      $table->foreignId('review_id')->nullable();
    });
  }
  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('books', function (Blueprint $table) {
      $table->dropColumn('blurb');
      $table->dropColumn('claimed_by_name');
      $table->dropColumn('page_count');
      $table->dropColumn('year');
      $table->dropColumn('review_id');
    });
  }
};









