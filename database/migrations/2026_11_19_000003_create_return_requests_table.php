<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained();
            $table->string('code')->unique();
            $table->string('type', 20);
            $table->string('reason', 100);
            $table->text('description')->nullable();
            $table->string('photo')->nullable();
            $table->string('status', 30)->default('baru');
            $table->text('admin_note')->nullable();
            $table->unsignedBigInteger('refund_amount')->nullable();
            $table->boolean('restocked')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_requests');
    }
};
