<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->cascadeOnDelete();
        });

        $defaultUserId = DB::table('users')->orderBy('id')->value('id');
        if ($defaultUserId) {
            DB::table('employees')
                ->whereNull('user_id')
                ->update(['user_id' => $defaultUserId]);
        }

        Schema::table('employees', function (Blueprint $table) {
            $table->dropUnique('employees_email_unique');
            $table->dropUnique('employees_document_unique');
            $table->dropUnique('employees_registration_number_unique');

            $table->unique(['user_id', 'email']);
            $table->unique(['user_id', 'document']);
            $table->unique(['user_id', 'registration_number']);
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropUnique('employees_user_id_email_unique');
            $table->dropUnique('employees_user_id_document_unique');
            $table->dropUnique('employees_user_id_registration_number_unique');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');

            $table->unique('email');
            $table->unique('document');
            $table->unique('registration_number');
        });
    }
};
