<?php
use App\Services\CategoryTree;
use App\Services\HtmlList;
use App\Services\ForSelectList;

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

    /**
     * @dataProvider arrayProvider
     */
   public function testCanProduceHtmlNestedCategories(array $after_conversion_db, array $db_result, string $html_list, array $html_select_list)
   {
        $html = new HtmlList;
        $html_select = new ForSelectList;
        $after_conversion_db = $html->convert($db_result);
        $this->assertEquals($html_list, $html->makeUlList($after_conversion_db)); 
        $this->assertEquals($html_select_list, $html_select->makeSelectList($after_conversion_db));
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
                '<li><a href="http://localhost:8000/show-category/1,Electronics">Electronics</a></li><li><a href="http://localhost:8000/show-category/2,Videos">Videos</a></li><li><a href="http://localhost:8000/show-category/3,Software">Software</a></li>',
                [
                    ['name'=>'Electronics'],
                    ['name'=>'Videos'],
                    ['name'=>'Software'],
                ]
 
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
                '<li><a href="http://localhost:8000/show-category/1,Electronics">Electronics</a><ul class="submenu menu vertical" data-submenu><li><a href="http://localhost:8000/show-category/2,Computers">Computers</a></li></ul></li>',
                [
                    ['name'=>'Electronics'],
                    ['name'=>'&nbsp;&nbsp;Computers'],
                ]
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
                '<li><a href="http://localhost:8000/show-category/1,Electronics">Electronics</a><ul class="submenu menu vertical" data-submenu><li><a href="http://localhost:8000/show-category/2,Computers">Computers</a><ul class="submenu menu vertical" data-submenu><li><a href="http://localhost:8000/show-category/3,Laptops">Laptops</a></li></ul></li></ul></li>',
                [
                    ['name'=>'Electronics'],
                    ['name'=>'&nbsp;&nbsp;Computers'],
                    ['name'=>'&nbsp;&nbsp;&nbsp;&nbsp;Laptops'],
                ]
            ],
        ];
    }
}
