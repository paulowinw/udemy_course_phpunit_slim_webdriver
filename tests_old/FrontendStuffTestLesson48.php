<?php
use PHPUnit\Framework\TestCase;
use Facebook\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverExpectedCondition;

class FrontendStuffTestLesson48 extends TestCase {
    protected $driver;

    public function setUp(): void {
        $options = new ChromeOptions();
        $options->addArguments(['--disable-dev-shm-usage']); // disable shared memory usage for Docker
        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        $this->driver = RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);
    }

    public function testCanSeeCorrectStringsOnMainPage(): void {
        $this->driver->get('http://localhost:8000');
        $this->assertStringContainsString('Add a new category', $this->driver->getPageSource());
    }

    public function testCanSeeConfirmDialogBoxWhenTryingToDeleteCategory(): void {
        $this->driver->get('http://localhost:8000/show-category/1');
        $this->driver->findElement(WebDriverBy::id('delete-category-confirmation'))->click();
        $this->driver->wait(4)->until(WebDriverExpectedCondition::alertIsPresent());
        $this->driver->switchTo()->alert()->dismiss();
        $this->assertTrue(true);
    }

    public function testCanSeeCorrectMessageAfterDeletingCategory(): void {
        $this->driver->get('http://localhost:8000/show-category/1');
        $this->driver->findElement(WebDriverBy::id('delete-category-confirmation'))->click();
        $this->driver->wait(4)->until(WebDriverExpectedCondition::alertIsPresent());
        $this->driver->switchTo()->alert()->accept();
        $this->assertStringContainsString('Category was deleted', $this->driver->getPageSource());
        $this->markTestIncomplete('Message about deleted category should appear after redirection');
    }

    public function testCanSeeEditAndDeleteLinksAndCategoryName(): void {
        $this->driver->get('http://localhost:8000');
        $electronics = $this->driver->findElement(WebDriverBy::linkText('Electronics'));
        $electronics->click();
        $h5 = $this->driver->findElement(WebDriverBy::cssSelector('div.basic-card-content h5'));
        $this->assertStringContainsString('Electronics', $h5->getText());
        $editLink = $this->driver->findElement(WebDriverBy::linkText('Edit'));
        $href = $editLink->getAttribute('href');
        $this->assertStringContainsString('edit-category/1', $href);
        $this->markTestIncomplete('Category name, description, edit, delete links must be dynamic');
    }

    public function testCanSeeEditCategoryMessage(): void {
        $this->driver->get('http://localhost:8000/show-category/1');
        $editLink = $this->driver->findElement(WebDriverBy::linkText('Edit'));
        $editLink->click();
        $this->assertStringContainsString('Edit category', $this->driver->getPageSource());
        $this->markTestIncomplete('Make input values dynamic');
    }

    public function tearDown(): void {
        $this->driver->quit();
    }
}
