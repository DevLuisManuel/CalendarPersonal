<?php

use App\Models\User;
use App\Utils\MigrationUtilsTrait;
use Illuminate\Database\{Migrations\Migration, Schema\Blueprint};
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private string $table = 'calendars';
    use MigrationUtilsTrait;

    public function up(): void
    {
        Schema::create($this->table, static function (Blueprint $table) {
            $table->id();
            $table->dateTime('appointmentDate');
            $table->foreignIdFor(User::class, 'userId');
        });
        $this->commons($this->table);
    }

    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
};
