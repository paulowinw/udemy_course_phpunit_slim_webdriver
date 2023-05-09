<?php
namespace App\Services;

class HtmlList extends CategoryTree {


    public function makeUlList(array $converted_db_array)
    {
        foreach ($converted_db_array as $value)
        {
            $this->categorylist .= '<li><a href="http://localhost:8000/show-category/'.$value['id'].','.$value['name'].'">'.$value['name'].'</a>';
            if (!empty($value['children']))
            {
                $this->categorylist .= '<ul class="submenu menu vertical" data-submenu>';
                $this->makeUlList($value['children']);
                $this->categorylist .= '</ul>';
            }
            $this->categorylist .= '</li>';
        }
        return $this->categorylist;
    }

}

