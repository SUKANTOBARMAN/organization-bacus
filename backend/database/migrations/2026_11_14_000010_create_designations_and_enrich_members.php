<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // পদবি: rank যত ছোট, তালিকায় তত উপরে (President = 1)
        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('name_bn')->nullable();
            $table->unsignedSmallInteger('rank')->default(100);
            $table->boolean('is_executive')->default(false);
            $table->timestamps();
        });

        Schema::table('members', function (Blueprint $table) {
            $table->foreignId('designation_id')->nullable()->after('name')->constrained()->nullOnDelete();
            $table->string('membership_type', 20)->default('general')->after('designation_id'); // general|life|associate|honorary
            $table->string('blood_group', 5)->nullable();
            $table->string('profession')->nullable();
            $table->string('workplace')->nullable();
            $table->string('gender', 10)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('bio')->nullable();
        });

        Schema::table('committee_members', function (Blueprint $table) {
            $table->foreignId('designation_id')->nullable()->after('member_id')->constrained()->nullOnDelete();
        });

        Schema::table('committees', function (Blueprint $table) {
            $table->string('type', 20)->default('executive')->after('title'); // executive|advisory|sub_committee
        });
    }

    public function down(): void
    {
        Schema::table('committees', fn (Blueprint $t) => $t->dropColumn('type'));
        Schema::table('committee_members', function (Blueprint $t) {
            $t->dropConstrainedForeignId('designation_id');
        });
        Schema::table('members', function (Blueprint $t) {
            $t->dropConstrainedForeignId('designation_id');
            $t->dropColumn(['membership_type', 'blood_group', 'profession', 'workplace', 'gender', 'date_of_birth', 'bio']);
        });
        Schema::dropIfExists('designations');
    }
};
