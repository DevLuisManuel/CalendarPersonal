<?php

use App\Utils\MigrationUtilsTrait;
use Illuminate\Database\{Migrations\Migration, Schema\Blueprint};
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private string $table = 'users';
    use MigrationUtilsTrait;

    public function up(): void
    {
        Schema::create($this->table, static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
        });
        $this->commons($this->table);
    }

    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
};
