<?php

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;

class FrontendStuffTest extends \PHPUnit\Framework\TestCase {

    protected $driver;

    public function setUp(): void
    {
        $this->driver = RemoteWebDriver::create('http://localhost:4444/wd/hub', [
            WebDriverCapabilityType::BROWSER_NAME => 'chrome',
            'chromeOptions' => ['w3c' => false]
        ]);
        $this->driver->get('http://localhost:8000');
    }

    public function testCanSeeCorrectStringsOnMainPage()
    {
        $this->assertStringContainsString('Add a new category', $this->driver->getPageSource());
    }

    public function testCanSeeConfirmDialogBoxWhenTryingToDeleteCategory()
    {
        $deleteConfirmationButton = $this->driver->findElement(WebDriverBy::id('delete-category-confirmation'));
        $deleteConfirmationButton->click();

        $this->driver->wait(4)->until(WebDriverExpectedCondition::alertIsPresent());
        $alert = $this->driver->switchTo()->alert();
        $alert->dismiss();

        $this->assertTrue(true);
    }

    public function testCanSeeCorrectMessageAfterDeletingCategory()
    {
        $deleteConfirmationButton = $this->driver->findElement(WebDriverBy::id('delete-category-confirmation'));
        $deleteConfirmationButton->click();

        $this->driver->wait(4)->until(WebDriverExpectedCondition::alertIsPresent());
        $alert = $this->driver->switchTo()->alert();
        $alert->accept();

        $this->assertStringContainsString('Category was deleted', $this->driver->getPageSource());

        $this->markTestIncomplete('Message about deleted category should appear after redirection');
    }

    public function tearDown(): void
    {
        if ($this->hasFailed()) {
            $this->driver->takeScreenshot(__DIR__ . '/error.png');
        }
        $this->driver->quit();
    }
}
