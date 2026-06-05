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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('personnel_id')
                ->constrained('personnels')
                ->onDelete('cascade');

            $table->date('date');
            $table->time('time');

            $table->enum('type', ['Ingreso', 'Salida']);
            $table->enum('status', ['Presente', 'Ausente']);

            // Por ahora no usamos turn_id porque aún no existe Turnos
            // $table->foreignId('turn_id')->nullable()->constrained('turns')->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
