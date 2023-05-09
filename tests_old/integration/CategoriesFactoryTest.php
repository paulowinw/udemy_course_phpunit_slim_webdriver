<?php
use App\Services\CategoriesFactory;

class CategoriesFactoryTest extends  PHPUnit\Framework\TestCase {

   public function testCanProduceStringBasedOnArray()
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
        
        $this->assertTrue(is_string(CategoriesFactory::create())); 
   }

}
