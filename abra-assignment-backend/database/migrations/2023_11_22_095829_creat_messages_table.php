<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Prompts\Table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("messages", function (Blueprint $table) {

            $table->increments('messageId');
            $table->tinyInteger('userSenderId');
            $table->tinyInteger('userRecivierId');
            $table->text('messageContent');
            $table->text('messageSubject');
            $table->tinyInteger('isRead');
            $table->timestamp('creationDate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
