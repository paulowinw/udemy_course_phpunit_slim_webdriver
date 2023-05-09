<?php
use App\Controllers\HomeController;
use App\Controllers\CategoryController;

$app->get('/', HomeController::class.':home');
$app->get('/delete-category/{id}', CategoryController::class.':deleteCategory');
$app->get('/show-category/{id}', CategoryController::class.':showCategory');
$app->get('/edit-category/{id}', CategoryController::class.':editCategory');
$app->post('/save-category', CategoryController::class.':saveCategory');
