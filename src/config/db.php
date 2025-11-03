<?php
// src/config/db.php

function get_pdo(): PDO
{
    // molt important: dins docker el host és el NOM del servei → "db"
    $host = 'db';
    $port = '3306';
    $db   = 'cimscat';   // posa-ho en minúscules si al docker-compose ho tens així
    $user = 'root';
    $pass = 'rootpass';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    return new PDO($dsn, $user, $pass, $opt);
}
