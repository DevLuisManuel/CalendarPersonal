<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\{Facades\Artisan, Facades\DB};

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('createDatabase', function () {
    $schemaName = config("database.connections.mysql.database");
    $this->comment("Create schema: " . $schemaName);
    $charset = config("database.connections.mysql.charset");
    $collation = config("database.connections.mysql.collation");
    config(["database.connections.mysql.database" => null]);
    $query = "CREATE DATABASE IF NOT EXISTS $schemaName CHARACTER SET $charset COLLATE $collation;";
    DB::statement($query);
    config(["database.connections.mysql.database" => $schemaName]);
});
