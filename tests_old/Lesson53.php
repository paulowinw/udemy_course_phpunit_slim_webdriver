<?php
use App\Services\CategoryTree;

class CategoryTreeTest extends  PHPUnit\Framework\TestCase {

    protected $category_tree;

    public function setUp()
    {
        $this->category_tree = new CategoryTree();
    }

    /**
     * @dataProvider arrayProvider
     */
   public function testCanConvertDatabaseResultToCategoryNestedArray($after_conversion, $db_result)
   {
        $this->assertEquals($after_conversion, $this->category_tree->convert($db_result)); 
   }

    public function arrayProvider()
    {
        return [
            'one level' => [
                [
                    ['id'=>1, 'name'=>'Electronics', 'parent_id'=>null, 'children'=>[]],
                    ['id'=>2, 'name'=>'Videos', 'parent_id'=>null, 'children'=>[]],
                    ['id'=>3, 'name'=>'Software', 'parent_id'=>null, 'children'=>[]]
                ],
                [
                    ['id'=>1, 'name'=>'Electronics', 'parent_id'=>null],
                    ['id'=>2, 'name'=>'Videos', 'parent_id'=>null],
                    ['id'=>3, 'name'=>'Software', 'parent_id'=>null]
                ],
            ],
            'two level' => [
                [
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
                ],
                [
                    ['id'=>1, 'name'=>'Electronics', 'parent_id'=>null],
                    ['id'=>2, 'name'=>'Computers', 'parent_id'=>1]
                ],
            ],
            'three level' => [
                [
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
                                'children'=>
                                    [
                                        [
                                            'id'=>3,
                                            'name'=>'Laptops',
                                            'parent_id'=>2,
                                            'children'=>[]
                                        ]
                                    ]
                            ]
                        ]
                    ]
                ],
                [
                    ['id'=>1, 'name'=>'Electronics', 'parent_id'=>null],
                    ['id'=>2, 'name'=>'Computers', 'parent_id'=>1],
                    ['id'=>3, 'name'=>'Laptops', 'parent_id'=>2],
                ],
            ],
        ];
    }
}
