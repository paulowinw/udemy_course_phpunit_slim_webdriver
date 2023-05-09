<?php

use App\Controllers\CategoryController;
use Slim\Container;
use App\Services\CategoriesFactory;

class CategoryControllerTest extends  PHPUnit\Framework\TestCase {

    public static $controller;

    public static function setUpBeforeClass()
    {
        $capsule = new \Illuminate\Database\Capsule\Manager;
        $capsule->addConnection([
            'driver' => 'sqlite',
            'host' => 'localhost',
            'database' => 'your path to db.sqlite',
            'username' => 'user',
            'password' => 'password',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => ''
        ]);
        $capsule->setAsGlobal(); // allow static methods
        $capsule->bootEloquent(); // setup the Eloquent ORM

        $container = new Container;
        $container['view'] = new \Slim\Views\PhpRenderer('./app/Views/',[
            'baseUrl' => 'http://localhost:8000/'
        ]);
        $categories = CategoriesFactory::create();
        $container->view->addAttribute('categories',$categories);
        self::$controller = new CategoryController($container);
    }

   public function testCanSeeEditedVideosCategory()
   {
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/show-category/13,Videos',
            'QUERY_STRING'=>'']
        );
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = self::$controller->showCategory($request, $response, ['id'=>13]);
        $this->assertContains('Description of Videos', (string) $response->getBody());
   }

}
