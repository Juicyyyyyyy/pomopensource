<?php

use App\Models\SettingCategory;
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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('setting_categories')->onDelete('cascade');
            $table->string('key')->unique();
            $table->string('name');
            $table->string('type'); // boolean/integer etc
            $table->text('options')->nullable();
            $table->text('description')->nullable();
            $table->text('default_value');
            $table->integer('display_order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
