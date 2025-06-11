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
            $table->string('fullName')->after('name');
            $table->string('userName')->unique()->after('fullName');
            $table->string('companyName')->after('userName');
            $table->string('phoneNumber')->after('companyName');
            $table->string('role')->default('user')->after('phoneNumber');
            $table->text('comments')->nullable()->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'fullName',
                'userName',
                'companyName',
                'phoneNumber',
                'role',
                'comments'
            ]);
        });
    }
};
