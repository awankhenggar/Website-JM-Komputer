<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code')->unique();
            $table->string('device_type', 30);
            $table->string('brand_model');
            $table->text('complaint');
            $table->string('service_type', 20)->default('umum');
            $table->string('handover', 20)->default('datang');
            $table->date('visit_date')->nullable();
            $table->string('status', 30)->default('booking');
            $table->text('diagnosis')->nullable();
            $table->unsignedBigInteger('estimate_cost')->nullable();
            $table->unsignedBigInteger('final_cost')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('service_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_request_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_updates');
        Schema::dropIfExists('service_requests');
    }
};
