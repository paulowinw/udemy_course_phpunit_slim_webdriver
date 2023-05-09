<?php
use PHPUnit\Framework\TestCase;
use Facebook\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverExpectedCondition;

class FrontendStuffTest extends TestCase
{
    private $webDriver;

    public function setUp(): void
    {
        $this->webDriver = RemotewebDriver::create(
            'http://localhost:4444/wd/hub',
            DesiredCapabilities::chrome()
        );
        $this->webDriver->get('http://localhost:8000');
    }

    public function tearDown(): void
    {
        $this->webDriver->quit();
    }

    public function testCanSeeCorrectStringsOnMainPage()
    {
        $this->assertStringContainsString('Add a new category', $this->webDriver->getPageSource());
    }

    public function testCanSeeConfirmDialogBoxWhenTryingToDeleteCategory()
    {
        $this->webDriver->get('http://localhost:8000/show-category/1');
        $this->webDriver->findElement(webDriverBy::id('delete-category-confirmation'))->click();
        $this->webDriver->wait(4, 100)->until(
            webDriverExpectedCondition::alertIsPresent()
        );
        $alert = $this->webDriver->switchTo()->alert();
        $alert->dismiss();

        $this->assertTrue(true);
    }

    public function testCanSeeCorrectMessageAfterDeletingCategory()
    {
        $this->webDriver->get('http://localhost:8000/show-category/1');
        $this->webDriver->findElement(webDriverBy::id('delete-category-confirmation'))->click();
        $this->webDriver->wait(4, 100)->until(
            webDriverExpectedCondition::alertIsPresent()
        );
        $alert = $this->webDriver->switchTo()->alert();
        $alert->accept();

        $this->assertStringContainsString('Category was deleted', $this->webDriver->getPageSource());

        $this->markTestIncomplete('Message about deleted category should appear after redirection');
    }

    public function testCanSeeEditAndDeleteLinksAndCategoryName()
    {
        $this->webDriver->get('http://localhost:8000');
        $electronics = $this->webDriver->findElement(webDriverBy::linkText('Electronics'));
        $electronics->click();
        $h5 = $this->webDriver->findElement(webDriverBy::cssSelector('div.basic-card-content h5'));
        $this->assertStringContainsString('Electronics', $h5->getText());

        $editLink = $this->webDriver->findElement(webDriverBy::linkText('Edit'));
        $href = $editLink->getAttribute('href');
        $this->assertStringContainsString('edit-category/1', $href);

        $this->markTestIncomplete('Category name, description, edit, delete links must be dynamic');
    }

    public function testCanSeeEditCategoryMessage()
    {
        $this->webDriver->get('http://localhost:8000/show-category/1');
        $editLink = $this->webDriver->findElement(webDriverBy::linkText('Edit'));
        $editLink->click();
        $this->assertStringContainsString('Edit category', $this->webDriver->getPageSource());

        $this->markTestIncomplete('Make input values dynamic');
    }

    public function testCanSeeFormValidation()
    {
        $this->webDriver->get('http://localhost:8000/');
        $button = $this->webDriver->findElement(webDriverBy::cssSelector('input[type="submit"]'));
        $button->click();
        $this->assertStringContainsString('Fill correctly the form', $this->webDriver->getPageSource());

        $this->webDriver->navigate()->back();
        $categoryName = $this->webDriver->findElement(webDriverBy::name('category_name'));
        $categoryName->sendKeys('Name');
        $categoryDescription = $this->webDriver->findElement(webDriverBy::name('category_description'));
        $categoryDescription->sendKeys('Description');
        $button = $this->webDriver->findElement(webDriverBy::cssSelector('input[type="submit"]'));
        $button->click();
        $this->assertStringContainsString('Category was saved', $this->webDriver->getPageSource());

        $this->markTestIncomplete('More jobs with html form needed');
    }

    public function testCanSeeNestedCategories()
    {
        $this->webDriver->get('http://localhost:8000');
        $categories = $this->webDriver->findElements(WebDriverBy::cssSelector('ul.dropdown li'));
        $this->assertEquals(18, count($categories));

        $elem1 = $this->webDriver->findElement(WebDriverBy::cssSelector('ul.dropdown > li:nth-child(2) > a'));
        $this->assertEquals('Electronics', $elem1->getText());

        $elem2 = $this->webDriver->findElement(WebDriverBy::cssSelector('ul.dropdown > li:nth-child(3) > a'));
        $this->assertEquals('Videos', $elem2->getText());

        $elem3 = $this->webDriver->findElement(WebDriverBy::cssSelector('ul.dropdown > li:nth-child(4) > a'));
        $this->assertEquals('Software', $elem3->getText());

        $elem4 = $this->webDriver->findElement(WebDriverBy::xpath('//ul[@class="dropdown menu"]/li[2]/ul[1]/li[1]/a'));
        $href = $elem4->getAttribute('href');
        $this->assertMatchesRegularExpression('@^http://localhost:8000/show-category/[0-9]+,Monitors$@', $href);

        $elem5 = $this->webDriver->findElement(WebDriverBy::xpath('//ul[@class="dropdown menu"]/li[2]//ul[1]//ul[1]//ul[1]//ul[1]/li[1]/a'));
        $href = $elem5->getAttribute('href');
        $this->assertMatchesRegularExpression('@^http://localhost:8000/show-category/[0-9]+,FullHD@', $href);
    }
}
