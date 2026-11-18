<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('election_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('position');           // যে পদে প্রার্থী
            $table->string('photo')->nullable();
            $table->unsignedInteger('votes')->default(0);
            $table->boolean('is_winner')->default(false);
            $table->timestamps();
        });

        Schema::create('membership_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 30);
            $table->text('address')->nullable();
            $table->string('profession')->nullable();
            $table->string('workplace')->nullable();
            $table->string('blood_group', 5)->nullable();
            $table->string('membership_type', 20)->default('general');
            $table->text('message')->nullable();
            $table->string('photo')->nullable();
            $table->string('status', 20)->default('pending'); // pending|approved|rejected
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_note')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();
            $table->index('status');
        });

        Schema::create('member_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20)->default('subscription'); // subscription|donation|admission_fee|other
            $table->decimal('amount', 10, 2);
            $table->date('paid_at');
            $table->string('period', 20)->nullable();            // 2026-11 বা 2026
            $table->string('method', 20)->default('cash');       // cash|bkash|nagad|bank|other
            $table->string('reference')->nullable();             // ট্রানজেকশন আইডি / রসিদ নম্বর
            $table->text('note')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['member_id', 'paid_at']);
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 30);      // created|updated|deleted|login|approved|rejected
            $table->string('subject', 60)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('summary')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('member_payments');
        Schema::dropIfExists('membership_applications');
        Schema::dropIfExists('election_candidates');
    }
};
