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
        $this->assertContains('Description of Electronics',$this->source());

    }


    public function testCanSeeEditCategoryMessage()
    {
        $this->url('show-category/1');
        $editLink = $this->byLinkText('Edit');
        $editLink->click();
        $this->assertContains('Edit category',$this->source());

        $this->markTestIncomplete('Make input values dynamic');
    }

    public function testCanSeeFormValidation()
    {
        $this->url('');
        $button = $this->byCssSelector('input[type="submit"]');
        $button->submit();
        $this->assertContains('Fill correctly the form',$this->source());

        $this->back();
        $categoryName = $this->byName('category_name');
        $categoryName->value('Name');
        $categoryDescription = $this->byName('category_description');
        $categoryDescription->value('Description');
        $button = $this->byCssSelector('input[type="submit"]');
        $button->submit();
        $this->assertContains('Category was saved',$this->source());

        $this->markTestIncomplete('More jobs with html form needed');
    }

    public function testCanSeeNestedCategories()
    {
        $this->url('');
        $categories = $this->elements($this->using('css selector')->value('ul.dropdown li'));
        $this->assertEquals(18, count($categories));

        $elem1 = $this->byCssSelector('ul.dropdown > li:nth-child(2) > a');
        $this->assertEquals('Electronics',$elem1->text());

        $elem2 = $this->byCssSelector('ul.dropdown > li:nth-child(3) > a');
        $this->assertEquals('Videos',$elem2->text());
        
        $elem3 = $this->byCssSelector('ul.dropdown > li:nth-child(4) > a');
        $this->assertEquals('Software',$elem3->text());

        // $elem4 = $this->byCssSelector('ul.dropdown > :nth-child(2) > :nth-child(2) > :nth-child(1) > a');
        $elem4 = $this->byXPath('//ul[@class="dropdown menu"]/li[2]/ul[1]/li[1]/a');
        $href = $elem4->attribute('href');
        $this->assertRegExp('@^http://localhost:8000/show-category/[0-9]+,Monitors$@',$href);

        $elem5 = $this->byXPath('//ul[@class="dropdown menu"]/li[2]//ul[1]//ul[1]//ul[1]//ul[1]/li[1]/a');
        $href = $elem5->attribute('href');
        $this->assertRegExp('@^http://localhost:8000/show-category/[0-9]+,FullHD@',$href);
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

        $this->assertNotRegExp('@^Computers</a>$@',$this->source());

        $this->assertContains('Category was deleted',$this->source());

        $this->markTestIncomplete('Message about deleted category should appear after redirection');

    }
}

