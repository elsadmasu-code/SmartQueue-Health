<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('patient')->after('password');
            $table->foreignId('facility_id')->nullable()->after('role')->constrained('facilities')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->after('facility_id')->constrained('departments')->nullOnDelete();
            $table->string('phone_number')->nullable()->unique()->after('email');
            $table->string('preferred_language')->default('en')->after('phone_number'); // en, am
            $table->boolean('is_active')->default(true)->after('preferred_language');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['facility_id']);
            $table->dropForeign(['department_id']);
            $table->dropColumn([
                'role',
                'facility_id',
                'department_id',
                'phone_number',
                'preferred_language',
                'is_active',
                'last_login_at',
            ]);
        });
    }
};
