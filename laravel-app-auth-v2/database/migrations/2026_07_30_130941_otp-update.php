<?php

use App\Models\User;
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
        // Schema::table('otps', function (Blueprint $table) {

        //     // update column type
        //     $table->foreignIdFor(User::class)
        //         ->nullable(true)
        //         ->change();

        //     // Update constrained
        //     $table->dropForeign(['user_id']);

        //     $table->foreign('user_id')
        //         ->references('id')
        //         ->on('users')
        //         ->constrained()
        //         ->cascadeOnDelete()
        //         ->cascadeOnUpdate();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('otps', function (Blueprint $table) {

        //     // update column type
        //     $table->foreignIdFor(User::class)
        //         ->nullable(false)
        //         ->change();

        //     $table->dropForeign(['user_id']);
        //     $table->foreign('user_id')
        //         ->references('id')
        //         ->on('users')
        //         ->constrained()
        //         ->cascadeOnDelete();
        // });
    }
};
