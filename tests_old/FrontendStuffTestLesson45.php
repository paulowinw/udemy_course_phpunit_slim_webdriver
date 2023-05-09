<?php

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use PHPUnit\Framework\TestCase;

class FrontendStuffTest extends TestCase {

    protected $webDriver;

    public function setUp(): void
    {
        $host = 'http://localhost:4444/wd/hub';
        $capabilities = DesiredCapabilities::chrome();
        $options = new \Facebook\WebDriver\Chrome\ChromeOptions();
        $options->addArguments(['--disable-gpu']);
        $capabilities->setCapability(\Facebook\WebDriver\Chrome\ChromeOptions::CAPABILITY, $options);
        $this->webDriver = RemoteWebDriver::create($host, $capabilities);
    }

    public function testCanSeeCorrectStringsOnMainPage()
    {
        $this->webDriver->get('http://localhost:8000');
        // var_dump($this->webDriver->getPageSource());exit;
        $this->assertStringContainsString('Add a new category', $this->webDriver->getPageSource());
    }

    public function tearDown(): void
    {
        $this->webDriver->quit();
    }
}
