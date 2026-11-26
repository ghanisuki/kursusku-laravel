<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 30)->nullable()->unique()->after('name');
            $table->string('role', 20)->default('user')->after('password');
            $table->string('status', 20)->default('active')->after('role');
            $table->string('phone', 20)->nullable();
            $table->string('position', 100)->nullable();
            $table->string('institution', 150)->nullable();
            $table->text('address')->nullable();
            $table->timestamp('last_login_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn([
                'username', 'role', 'status', 'phone',
                'position', 'institution', 'address', 'last_login_at',
            ]);
        });
    }
};
