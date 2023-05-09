<?php

class FrontendStuffTest extends PHPUnit_Extensions_Selenium2TestCase {

    public function setUp()
    {
        $this->setBrowserUrl('http://localhost:8000');
        $this->setBrowser('chrome');
        $this->setDesiredCapabilities(['chromeOptions' => ['w3c' => false]]); // phpunit-selenium does not support W3C mode yet
    }

    public function testCanSeeCorrectStringsOnMainPage()
    {
        $this->url('');
        $this->assertContains('Add a new category',$this->source());
    }

    public function testCanSeeConfirmDialogBoxWhenTryingToDeleteCategory()
    {
        $this->url('show-category/1');
        $this->clickOnElement('delete-category-confirmation');
        $this->waitUntil(function () {
            if($this->alertIsPresent()) return true;
            return null;
        }, 4000);
        $this->dismissAlert();

        $this->assertTrue(true);

    }


    public function testCanSeeCorrectMessageAfterDeletingCategory()
    {
        $this->url('show-category/1');
        $this->clickOnElement('delete-category-confirmation');
        $this->waitUntil(function () {
            if($this->alertIsPresent()) return true;
            return null;
        }, 4000);
        $this->acceptAlert();

        $this->assertContains('Category was deleted',$this->source());

        $this->markTestIncomplete('Message about deleted category should appear after redirection');

    }

    public function testCanSeeEditAndDeleteLinksAndCategoryName()
    {
        $this->url('');
        $electronics = $this->byLinkText('Electronics');
        $electronics->click();
        $h5 = $this->byCssSelector('div.basic-card-content h5');
        $this->assertContains('Electronics',$h5->text());

        $editLink = $this->byLinkText('Edit');
        $href = $editLink->attribute('href');
        $this->assertContains('edit-category/1',$href);

        $this->markTestIncomplete('Category name, description, edit, delete links must be dynamic');
    }
}
