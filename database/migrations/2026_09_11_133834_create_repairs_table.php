<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('device_id')->constrained('devices');
            $table->string('status');
            $table->text('problem_description');
            $table->text('diagnosis')->nullable();
            $table->text('repair_description')->nullable();
            $table->decimal('estimated_price', 6)->nullable();
            $table->decimal('final_price', 6)->nullable();
            $table->timestamp('received_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repairs');
    }
};
