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

            $table->enum('type', ['noche', 'cargo_extra', 'dano', 'servicio'])->default('cargo_extra');
            $table->string('description', 200);
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('amount', 10, 2);
            $table->timestamp('charged_at')->useCurrent();
            $table->softDeletes();
            $table->timestamps();

            $table->index('reservation_id');
            $table->index(['reservation_id', 'type']);
            $table->index('charged_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('charges');
    }
};