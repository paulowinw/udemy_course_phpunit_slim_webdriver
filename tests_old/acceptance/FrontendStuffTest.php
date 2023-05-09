<?php

use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;

class FrontendStuffTest extends TestCase
{
    protected $driver;

    public function setUp(): void
    {
        $options = new ChromeOptions();
        $options->addArguments(['--disable-dev-shm-usage']); // Disable shared memory usage to prevent issues on headless systems
        $options->setExperimentalOption('w3c', false); // php-webdriver does not support W3C mode yet
        $capabilities = [WebDriverCapabilityType::BROWSER_NAME => 'chrome', ChromeOptions::CAPABILITY => $options];
        $this->driver = RemoteWebDriver::create(
            'http://localhost:4444/wd/hub',
            // DesiredCapabilities::chrome()
            $capabilities
        );
    }

    public function testCanSeeCorrectStringsOnMainPage()
    {
        $this->driver->get('http://localhost:8000');
        $this->assertStringContainsString('Add a new category', $this->driver->getPageSource());
    }

    // public function testCanSeeConfirmDialogBoxWhenTryingToDeleteCategory()
    // {
    //     $this->driver->get('http://localhost:8000');
    //     // $this->driver->findElement(WebDriverBy::className('menu'))->click();
    //     $ele = $this->driver->findElement(WebDriverBy::id('delete-category-confirmation'));
    //     var_dump($ele);
    //     $this->driver->findElement(WebDriverBy::id('delete-category-confirmation'))->click();
    //     $this->driver->wait(4)->until(
    //         WebDriverExpectedCondition::alertIsPresent()
    //     );
    //     $this->driver->switchTo()->alert()->dismiss();
    //     $this->assertTrue(true);
    // }

    // public function testCanSeeCorrectMessageAfterDeletingCategory()
    // {
    //     $this->driver->get('http://localhost:8000');
    //     $this->driver->findElement(WebDriverBy::id('delete-category-confirmation'))->click();
    //     $this->driver->wait(4)->until(
    //         WebDriverExpectedCondition::alertIsPresent()
    //     );
    //     $this->driver->switchTo()->alert()->accept();
    //     $this->assertStringContainsString('Category was deleted', $this->driver->getPageSource());
    //     $this->markTestIncomplete('Message about deleted category should appear after redirection');
    // }

    public function testFindDivGridContainer()
    {
        $this->driver->get('http://localhost:8000');
        var_dump(WebDriverBy::className('grid-container'));
        $this->driver->findElement(WebDriverBy::className('grid-container'));
    }

    public function tearDown(): void
    {
        $this->driver->quit();
    }
}
