<?php
namespace App\Services;

use App\Models\Category;
use App\Services\HtmlList;

class CategoriesFactory {
    
    public static function create(): string
    {
        // get categories from database
        // convert result to nested array
        // convert to string


        $categories = Category::all()->toArray();
        // $htmlList = new class {
        //     public function convert(array $categories)
        //     {
        //         return [];
        //     }

        //     public function makeUlList(array $converted_array)
        //     {
        //         return '';
        //     }
        // };
        $htmlList = new HtmlList();
        $converted_array = $htmlList->convert($categories);
        return $htmlList->makeUlList($converted_array);

    }
}
