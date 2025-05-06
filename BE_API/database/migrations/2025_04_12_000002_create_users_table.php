<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->foreignUuid('university_id')
                ->constrained('universities')
                ->onUpdate('cascade')
                ->onDelete('cascade')
                ->unique();
            $table->string('phone');
            $table->enum('role', ['User', 'Admin']);
            $table->enum('approval', ['Pending', 'Approved'])->default('Pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}
