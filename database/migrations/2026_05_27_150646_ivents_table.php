<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('poster_path')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('max_winners')->default(3);
            $table->text('rules')->nullable();
            $table->string('auto_tag')->nullable();
            // auto_tag = tag yang otomatis ditambahkan saat ikut event, contoh: SunsetMay2026
            $table->enum('status', ['draft', 'active', 'ended'])->default('draft');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};