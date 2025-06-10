<?php
use Illuminate\Database\Capsule\Manager as Capsule;

// Initialiser Capsule (qui est le gestionnaire d'Eloquent)
$capsule = new Capsule;

// Configuration de la connexion à la base de données
$capsule->addConnection([
    'driver'    => 'mysql', // ou 'pgsql', 'sqlite', 'sqlsrv'
    'host'      => '127.0.0.1',
    'database'  => 'cb',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_general_ci',
    'prefix'    => '',
]);

// Démarrer Eloquent
$capsule->setAsGlobal();
$capsule->bootEloquent();