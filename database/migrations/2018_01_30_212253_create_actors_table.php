<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActorsTable extends Migration
{
    public function up(): void
    {
        Schema::create('actors', function (Blueprint $table) {
            $table->string('uuid', 36)->unique();
			$table->char('first_name', 40);
			$table->char('last_name', 40);
			$table->char('country', 2);

            $table->primary(['first_name', 'last_name', 'country']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actors');
    }
}
