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
            $table->string('userLogo')->nullable()->after('userName');
            $table->string('userLogoPath')->nullable()->after('userLogo');
            $table->string('companyName')->after('userLogoPath');
            $table->string('companyLogo')->nullable()->after('companyName');
            $table->string('companyLogoPath')->nullable()->after('companyLogo');
            $table->string('phoneNumber')->after('companyLogoPath');
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
                'userLogo',
                'userLogoPath',
                'companyName',
                'companyLogo',
                'companyLogoPath',
                'phoneNumber',
                'role',
                'comments'
            ]);
        });
    }
};
