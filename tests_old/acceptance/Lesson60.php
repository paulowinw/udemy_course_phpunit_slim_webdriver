<?php
use App\Models\Category;

class BackendStuffTest extends PHPUnit_Extensions_Selenium2TestCase {

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

        $capsule::schema()->dropIfExists('categories');
        
        $capsule::schema()->create('categories', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable(false);
            $table->bigInteger('parent_id')->unsigned()->nullable();
        });
        // $capsule::table('categories')->insert(
        //     ['name' => 'Electronics']
        // );
    }

    public function setUp()
    {
        $this->setBrowserUrl('http://localhost:8000');
        $this->setBrowser('chrome');
        $this->setDesiredCapabilities(['chromeOptions' => ['w3c' => false]]); // phpunit-selenium does not support W3C mode yet
    }

    public function testCanSeeAddedCategories()
    {
        Category::create([
            'name'=>'Electronics'
        ]);

        $this->url('');

        $element = $this->byXPath('//ul[@class="dropdown menu"]/li[2]/a');
        $href = $element->attribute('href');
        $this->assertRegExp('@^http://localhost:8000/show-category/[0-9]+,Electronics@',$href);

        $this->url('show-category/1');
        $element = $this->byXPath('//ul[@class="dropdown menu"]/li[2]/a');
        $href = $element->attribute('href');
        $this->assertRegExp('@^http://localhost:8000/show-category/[0-9]+,Electronics@',$href);

    }

    public function testCanAddChildCategory()
    {
        $parent_category = Category::where('name','Electronics')->first();

        $child_category = new Category;
        $child_category->name = 'Monitors';

        $parent_category->children()->save($child_category);

        $element = $this->byXPath('//ul[@class="dropdown menu"]/li[2]/ul[1]/li[1]/a');
        $href = $element->attribute('href');
        $this->assertRegExp('@^http://localhost:8000/show-category/[0-9]+,Monitors@',$href);
    }

    
}
