<?php

/* public PDO::__construct ( string $dsn [, string $username [, string $passwd [, array $options ]]] )*/

$dsn = 'mysql:host=localhost;dbname=webreath';
$user = 'root';
$password = 'Webreath159357';

try {
    $dbh = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    echo 'Connexion échouée : ' . $e->getMessage();
}