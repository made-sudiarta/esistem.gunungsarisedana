<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('masters', function (Blueprint $table) {
            $table->id();
            $table->string('group')->index();
            $table->string('key')->index();
            $table->text('value')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['group', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('masters');
    }
};