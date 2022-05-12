<?php

namespace App\Utils;

use App\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\{Facades\DB, Facades\Schema};
use PDO;
use RuntimeException;

trait MigrationUtilsTrait
{
    public function commons(string $table, string $comment = ""): void
    {
        $this->timeStamps($table);
        if(trim($comment)!=="") $this->comment($table, $comment);
    }

    private function timeStamps(string $table): void
    {
        Schema::table($table, static function (Blueprint $table) {
            $table->dateTime(Model::CREATED_AT)->nullable();
            $table->dateTime(Model::UPDATED_AT)->nullable();
        });
    }

    private function comment(string $table, string $comment): void
    {
        $pdo = DB::connection()->getPdo();
        $comment = $pdo->quote($comment);
        if (!$comment) {
            throw new RuntimeException(sprintf('Comentario "%s" no es valido en: "%s"', $comment, $table));
        }
        DB::statement("ALTER TABLE $table comment $comment");
    }

}
