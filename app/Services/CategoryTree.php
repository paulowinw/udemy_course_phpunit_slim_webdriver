<?php
namespace App\Services;

class CategoryTree {

    public $categorylist;

    public function convert(array $db_array, int $parent_id = null): array
    {

        $nested_categories = array();

        foreach ($db_array as $category)
        {
            $category['children'] = [];
            if ($category['parent_id'] == $parent_id)
            {
                $children = $this->convert($db_array, $category['id']);
                if ($children)
                {
                    $category['children'] = $children;
                }
                $nested_categories[] = $category;
            }
        }
        return $nested_categories;

    }

}

