<?php
use App\Services\CategoryTree;

class CategoryTreeTest extends  PHPUnit\Framework\TestCase {

   public function testCanConvertDatabaseResultToCategoryArray()
   {
       $tree = new CategoryTree();

       $db_result = [
            ['id'=>1, 'name'=>'Electronics', 'parent_id'=>null],
            ['id'=>2, 'name'=>'Videos', 'parent_id'=>null],
            ['id'=>3, 'name'=>'Software', 'parent_id'=>null]
       ];

       $after_conversion = [
            ['id'=>1, 'name'=>'Electronics', 'parent_id'=>null, 'children'=>[]],
            ['id'=>2, 'name'=>'Videos', 'parent_id'=>null, 'children'=>[]],
            ['id'=>3, 'name'=>'Software', 'parent_id'=>null, 'children'=>[]]
       ];

        $this->assertEquals($after_conversion, $tree->convert($db_result));
       
   }

   public function testCanConvertDatabaseResultToOneLevelNestedArray()
   {
       $tree = new CategoryTree();

       $db_result = [
            ['id'=>1, 'name'=>'Electronics', 'parent_id'=>null],
            ['id'=>2, 'name'=>'Computers', 'parent_id'=>1]
       ];


       $after_conversion = [
            [
                'id'=>1,
                'name'=>'Electronics',
                'parent_id'=>null,
                'children'=>
                    [
                        [
                            'id'=>2,
                            'name'=>'Computers',
                            'parent_id'=>1,
                            'children'=>[]
                        ]
                    ]
            ]
       ];

        $this->assertEquals($after_conversion, $tree->convert($db_result));
       
   }


}
