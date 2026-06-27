<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedule_changes', function (Blueprint $table) {
            $table->string('change_type')->nullable()->after('user_id');
            $table->text('previous_value')->nullable()->after('change_type');
            $table->text('new_value')->nullable()->after('previous_value');
        });
    }

    public function down(): void
    {
        Schema::table('schedule_changes', function (Blueprint $table) {
            $table->dropColumn([
                'change_type',
                'previous_value',
                'new_value',
            ]);
        });
    }
};