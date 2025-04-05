<?php

use Illuminate\Support\Facades\DB;

// Connect to the default PostgreSQL database
$pdo = new PDO(
    "pgsql:host=" . env('DB_HOST', 'pgsql') . ";port=" . env('DB_PORT', '5432') . ";dbname=postgres",
    env('DB_USERNAME', 'sail'),
    env('DB_PASSWORD', 'password')
);

// Create testing database if it doesn't exist
try {
    $pdo->exec("CREATE DATABASE testing");
} catch (PDOException $e) {
    if ($e->getCode() !== '42P04') { // 42P04 is the error code for duplicate database
        throw $e;
    }
}

// Connect to the testing database
$pdo = new PDO(
    "pgsql:host=" . env('DB_HOST', 'pgsql') . ";port=" . env('DB_PORT', '5432') . ";dbname=testing",
    env('DB_USERNAME', 'sail'),
    env('DB_PASSWORD', 'password')
);

// Enable pgvector extension
$pdo->exec('CREATE EXTENSION IF NOT EXISTS vector'); 