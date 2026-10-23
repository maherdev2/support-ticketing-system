<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->after('id')->nullable()->constrained('roles')->nullOnDelete();
            $table->foreignId('department_id')->after('role_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('phone')->nullable()->after('email');
            $table->string('employee_id')->nullable()->unique()->after('phone');
            $table->boolean('is_active')->default(true)->after('remember_token');
            $table->timestamp('last_login_at')->nullable()->after('is_active');

            $table->index('role_id');
            $table->index('department_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['department_id']);
            $table->dropColumn([
                'role_id',
                'department_id',
                'phone',
                'employee_id',
                'is_active',
                'last_login_at'
            ]);
        });
    }
};
