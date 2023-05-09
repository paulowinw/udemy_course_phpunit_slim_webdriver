<?php

use App\Models\Category;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\TestCase;

class BackendStuffTest extends TestCase
{
    protected $driver;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $capsule = new \Illuminate\Database\Capsule\Manager;
        $capsule->addConnection([
            'driver' => 'sqlite',
            'host' => 'localhost',
            'database' => 'D:\Projects\_studies\udemy_phpunit_selenium\practice_slim_webdriver\app\database\db.sqlite',
            'username' => 'user',
            'password' => 'password',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => ''
        ]);
        $capsule->setAsGlobal(); // allow static methods
        $capsule->bootEloquent(); // setup the Eloquent ORM

        $schema = $capsule->schema();
        $schema->dropIfExists('categories');

        $schema->create('categories', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable(false);
            $table->text('description')->nullable(false);
            $table->bigInteger('parent_id')->unsigned()->nullable();
        });

        // $capsule::table('categories')->insert(
        //     ['name' => 'Electronics']
        // );
    }


    public function setUp(): void
    {
        $this->driver = RemoteWebDriver::create('http://localhost:4444/wd/hub', DesiredCapabilities::chrome());
    }

    public function tearDown(): void
    {
        $this->driver->quit();
    }

    public function testCanSeeAddedCategories()
    {
        Category::create([
            'name' => 'Electronics',
            'description' => 'Description of Electronics',
        ]);

        $this->driver->get('http://localhost:8000');

        $element = $this->driver->findElement(WebDriverBy::xpath('//ul[@class="dropdown menu"]/li[2]/a'));
        $href = $element->getAttribute('href');
        $this->assertMatchesRegularExpression ('@^http://localhost:8000/show-category/[0-9]+,Electronics@', $href);

        $this->driver->get('http://localhost:8000/show-category/1');
        $element = $this->driver->findElement(WebDriverBy::xpath('//ul[@class="dropdown menu"]/li[2]/a'));
        $href = $element->getAttribute('href');
        $this->assertMatchesRegularExpression ('@^http://localhost:8000/show-category/[0-9]+,Electronics@', $href);
    }

    public function testCanAddChildCategory()
    {
        $electronics = Category::where('name', 'Electronics')->first();
        $electronics->children()->saveMany([
            new Category(['name' => 'Monitors', 'description' => 'Description of Monitors']),
            new Category(['name' => 'Tablets', 'description' => 'Description of Tablets']),
            new Category(['name' => 'Computers', 'description' => 'Description of Computers']),
        ]);

        $computers = Category::where('name', 'Computers')->first();
        $computers->children()->saveMany([
            new Category(['name' => 'Desktops', 'description' => 'Description of Desktops']),
            new Category(['name' => 'Notebooks', 'description' => 'Description of Notebooks']),
            new Category(['name' => 'Laptops', 'description' => 'Description of Laptops']),
        ]);

        $laptops = Category::where('name', 'Laptops')->first();
        $laptops->children()->saveMany([
            new Category(['name' => 'Asus', 'description' => 'Description of Asus']),
            new Category(['name' => 'Dell', 'description' => 'Description of Dell']),
            new Category(['name' => 'Acer', 'description' => 'Description of Acer']),
        ]);

        $acer = Category::where('name', 'Acer')->first();
        $acer->children()->saveMany([
            new Category(['name' => 'FullHD', 'description' => 'Description of FullHD']),
            new Category(['name' => 'HD+', 'description' => 'Description of HD+'])
        ]);

        Category::create([
            'name' => 'Videos',
            'description' => 'Description of Videos',
        ]);
        Category::create([
            'name' => 'Software',
            'description' => 'Description of Software',
        ]);

        $software = Category::where('name', 'Software')->first();
        $software->children()->saveMany([
            new Category(['name' => 'Operating systems', 'description' => 'Description of Operating systems']),
            new Category(['name' => 'Servers', 'description' => 'Description of Servers'])
        ]);

        $operating_systems = Category::where('name', 'Operating systems')->first();
        $operating_systems->children()->saveMany([
            new Category(['name' => 'Linux', 'description' => 'Description of Linux'])
        ]);

        $this->driver->get('http://localhost:8000');

        $element = $this->driver->findElement(WebDriverBy::xpath('//ul[@class="dropdown menu"]/li[2]/ul[1]/li[1]/a'));
        $href = $element->getAttribute('href');
        $this->assertMatchesRegularExpression ('@^http://localhost:8000/show-category/[0-9]+,Monitors@', $href);
    }
}
