<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('unit_number', 10)->unique();
            $table->string('row_label', 5);
            $table->string('size_label', 20);
            $table->integer('size_sqft');
            $table->decimal('monthly_rate', 8, 2);
            $table->string('status', 20)->default('available');
            $table->integer('floor')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
