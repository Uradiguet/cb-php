<?php
require './../../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Reliese\Coders\Model\Factory;
use Reliese\Support\Classify;
use Reliese\Coders\Model\Config;

// Initialiser Capsule avec la configuration de la base de données
$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'database'  => 'cb',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_general_ci',
    'prefix'    => '',
],'default');

// Initialiser le gestionnaire d'événements et conteneur pour Eloquent
$capsule->setEventDispatcher(new Dispatcher(new Container));
$capsule->setAsGlobal();
$capsule->bootEloquent();

$tables = Capsule::schema()->getTables();

// Charger le gestionnaire de base de données
$dbManager = $capsule->getDatabaseManager();

// Instancier les autres dépendances nécessaires
$filesystem = new Filesystem;
$classify = new Classify;
$config = new Config([
    '*.path' => __DIR__ . '/../models', // Répertoire pour les modèles
    '*.namespace' => 'App\models',  // Namespace pour les modèles
    '*.parent' => Illuminate\Database\Eloquent\Model::class,
    '*.use' => []
]);

// Créer une instance de la classe Factory avec toutes les dépendances
$factory = new Factory($dbManager, $filesystem, $classify, $config);

// Générer les modèles à partir de la base de données
foreach ($tables as $table) {
    $factory->on('default')->create('cb', $table['name']);
}