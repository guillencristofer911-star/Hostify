<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('charges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('reservation_id')->constrained()->onDelete('cascade');
            $table->foreignId('registered_by')->nullable()->constrained('users')->onDelete('set null');
            $table->index('registered_by');
            $table->string('description', 200);
            $table->decimal('amount', 10, 2);
            $table->timestamp('charged_at');
            $table->softDeletes();
            $table->timestamps();

            $table->index('reservation_id');
            $table->index('charged_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('charges');
    }
};