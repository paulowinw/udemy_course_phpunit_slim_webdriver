<?php
use App\Services\CategoriesFactory;

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal(); // allow static methods
$capsule->bootEloquent(); // setup the Eloquent ORM

$categories = CategoriesFactory::create();
$container->view->addAttribute('categories',$categories);
