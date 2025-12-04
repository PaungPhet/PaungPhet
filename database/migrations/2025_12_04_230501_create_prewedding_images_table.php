<?php

use App\Models\Wedding;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('prewedding_images', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Wedding::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('path');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['wedding_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prewedding_images');
    }
};
