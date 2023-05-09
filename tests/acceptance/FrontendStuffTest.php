<?php
class FrontendStuffTest extends PHPUnit\Framework\TestCase
{
    protected $webDriver;

    protected function setUp(): void
    {
        $capabilities = \Facebook\WebDriver\Remote\DesiredCapabilities::chrome();
        $options = new \Facebook\WebDriver\Chrome\ChromeOptions();
        $options->setExperimentalOption('w3c', false);
        $capabilities->setCapability(\Facebook\WebDriver\Chrome\ChromeOptions::CAPABILITY, $options);

        $this->webDriver = \Facebook\WebDriver\Remote\RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);
        $this->webDriver->get('http://localhost:8000');
    }

    protected function tearDown(): void
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
        $this->webDriver->findElement(\Facebook\WebDriver\WebDriverBy::id('delete-category-confirmation'))->click();

        $this->webDriver->wait()->until(
            \Facebook\WebDriver\WebDriverExpectedCondition::alertIsPresent()
        );
        $alert = $this->webDriver->switchTo()->alert();
        $alert->dismiss();

        $this->assertTrue(true);
    }

    public function testCanSeeEditAndDeleteLinksAndCategoryName()
    {
        $this->webDriver->get('http://localhost:8000');
        $electronics = $this->webDriver->findElement(\Facebook\WebDriver\WebDriverBy::linkText('Electronics'));
        $electronics->click();

        $h5 = $this->webDriver->findElement(\Facebook\WebDriver\WebDriverBy::cssSelector('div.basic-card-content h5'));
        $this->assertStringContainsString('Electronics', $h5->getText());

        $editLink = $this->webDriver->findElement(\Facebook\WebDriver\WebDriverBy::linkText('Edit'));
        $href = $editLink->getAttribute('href');
        $this->assertStringContainsString('edit-category/1', $href);

        $this->markTestIncomplete('Category name, description, edit, delete links must be dynamic');
    }

    public function testCanSeeEditCategoryMessage()
    {
        $this->webDriver->get('http://localhost:8000/show-category/1');
        $editLink = $this->webDriver->findElement(\Facebook\WebDriver\WebDriverBy::linkText('Edit'));
        $editLink->click();
        $this->assertStringContainsString('Edit category', $this->webDriver->getPageSource());

        $this->markTestIncomplete('Make input values dynamic');
    }

    public function testCanSeeFormValidation()
    {
        $this->webDriver->get('http://localhost:8000');
        $button = $this->webDriver->findElement(\Facebook\WebDriver\WebDriverBy::cssSelector('input[type="submit"]'));
        $button->submit();
        $this->assertStringContainsString('Fill correctly the form', $this->webDriver->getPageSource());

        $this->webDriver->navigate()->back();
        $categoryName = $this->webDriver->findElement(\Facebook\WebDriver\WebDriverBy::name('category_name'));
        $categoryName->sendKeys('Name');
        $categoryDescription = $$this->webDriver->findElement(\Facebook\WebDriver\WebDriverBy::name('category_description'))->sendKeys('Description');
        $button = $this->webDriver->findElement(\Facebook\WebDriver\WebDriverBy::cssSelector('input[type="submit"]'));
        $button->submit();
        $this->assertStringContainsString('Category was saved', $this->webDriver->getPageSource());

        $this->markTestIncomplete('More jobs with html form needed');
    }

    public function testCanSeeNestedCategories()
    {
        $this->webDriver->get('http://localhost:8000');
        $categories = $this->webDriver->findElements(\Facebook\WebDriver\WebDriverBy::cssSelector('ul.dropdown li'));
        $this->assertCount(18, $categories);

        $elem1 = $this->webDriver->findElement(\Facebook\WebDriver\WebDriverBy::cssSelector('ul.dropdown > li:nth-child(2) > a'));
        $this->assertEquals('Electronics', $elem1->getText());

        $elem2 = $this->webDriver->findElement(\Facebook\WebDriver\WebDriverBy::cssSelector('ul.dropdown > li:nth-child(3) > a'));
        $this->assertEquals('Videos', $elem2->getText());

        $elem3 = $this->webDriver->findElement(\Facebook\WebDriver\WebDriverBy::cssSelector('ul.dropdown > li:nth-child(4) > a'));
        $this->assertEquals('Software', $elem3->getText());

        $elem4 = $this->webDriver->findElement(\Facebook\WebDriver\WebDriverBy::xpath('//ul[@class="dropdown menu"]/li[2]/ul[1]/li[1]/a'));
        $href = $elem4->getAttribute('href');
        $this->assertMatchesRegularExpression ('@^http://localhost:8000/show-category/[0-9]+,Monitors$@', $href);

        $elem5 = $this->webDriver->findElement(\Facebook\WebDriver\WebDriverBy::xpath('//ul[@class="dropdown menu"]/li[2]//ul[1]//ul[1]//ul[1]//ul[1]/li[1]/a'));
        $href = $elem5->getAttribute('href');
        $this->assertMatchesRegularExpression ('@^http://localhost:8000/show-category/[0-9]+,FullHD$@', $href);
    }

    public function testCanSeeCorrectMessageAfterDeletingCategory()
    {
        $this->webDriver->get('http://localhost:8000/show-category/1');
        $this->webDriver->findElement(\Facebook\WebDriver\WebDriverBy::id('delete-category-confirmation'))->click();

        $this->webDriver->wait()->until(
            \Facebook\WebDriver\WebDriverExpectedCondition::alertIsPresent()
        );
        $alert = $this->webDriver->switchTo()->alert();
        $alert->accept();

        $this->assertStringContainsString('Category was deleted', $this->webDriver->getPageSource());

        $this->webDriver->get('http://localhost:8000');
        $this->assertDoesNotMatchRegularExpression('/Computers<\/a>/', $this->webDriver->getPageSource());

        $this->markTestIncomplete('Message about deleted category should appear after redirection');
    }
}

