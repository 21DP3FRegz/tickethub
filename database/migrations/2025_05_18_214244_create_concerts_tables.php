<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->timestamps();
        });

        Schema::create('concerts', function (Blueprint $table) {
            $table->id();
            $table->string('artist', 100);
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('shows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('concert_id')->constrained()->onDelete('cascade');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->timestamps();
        });

        Schema::create('rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('show_id')->constrained()->onDelete('cascade');
            $table->string('name', 50);
            $table->integer('order');
            $table->integer('total_seats')->nullable();
            $table->unique(['show_id', 'name']);
            $table->unique(['show_id', 'order']);
            $table->timestamps();
        });

        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('show_id')->constrained()->onDelete('cascade');
            $table->foreignId('row_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('seat_number')->nullable();
            $table->string('label', 50)->nullable();
            $table->unique(['show_id', 'row_id', 'seat_number']);
            $table->unique(['show_id', 'label']);
            $table->timestamps();
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('show_id')->constrained()->onDelete('cascade');
            $table->foreignId('seat_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('reservation_token')->index();
            $table->dateTime('reserved_until');
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name', 100);
            $table->string('address', 100);
            $table->string('city', 100);
            $table->string('zip', 10);
            $table->string('country', 100);
            $table->timestamps();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('show_id')->constrained()->onDelete('cascade');
            $table->foreignId('seat_id')->constrained()->onDelete('cascade');
            $table->string('code', 10)->unique();
            $table->string('name', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('seats');
        Schema::dropIfExists('rows');
        Schema::dropIfExists('shows');
        Schema::dropIfExists('concerts');
        Schema::dropIfExists('locations');
    }
};
