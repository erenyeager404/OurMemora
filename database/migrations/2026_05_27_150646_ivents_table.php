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
            $table->text('prize_description')->nullable();
            $table->string('poster_path')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('max_winners')->default(3);
            $table->text('rules')->nullable();
            $table->string('auto_tag')->nullable();
            // Status: draft(hanya admin), active(buka), voting(tutup submit), ended(selesai)
            $table->enum('status', ['draft', 'active', 'voting', 'ended'])->default('draft');
            $table->timestamps();
        });

        Schema::create('event_participations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('photo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unique(['event_id', 'user_id']);
            // 1 user hanya bisa submit 1 foto per event
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('event_participations');
        Schema::dropIfExists('events');
    }
};