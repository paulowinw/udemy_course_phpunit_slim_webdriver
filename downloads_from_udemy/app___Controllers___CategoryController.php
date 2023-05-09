<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Category;

class CategoryController extends BaseController {


    public function deleteCategory($request, $response, $args)
    {
        $category_id = $args['id'];

        $category = Category::find($category_id);
        $category->delete();
    
        $response = $this->container->view->render($response, 'view.phtml',['category_deleted'=>true]);

        return $response;
    }

    public function showCategory($request, $response, $args)
    {
        $category_id = $args['id'];
        // TO DO: get category by id from the database
        $category = 'Electronics';
    
        $response = $this->container->view->render($response, 'view.phtml',['category'=>$category]);

        return $response;
    }

    public function editCategory($request, $response, $args)
    {
        $category_id = $args['id'];
        // TO DO: get category by id from the database
        $category = ['name'=>'Electronics','parent'=>null];
    
        $response = $this->container->view->render($response, 'view.phtml',['editedCategory'=>$category]);

        return $response;
    }

    public function saveCategory($request, $response, $args)
    {
        $data = $request->getParsedBody();
        if(empty($data['category_name']) || empty($data['category_description']))
        $categorySaved = false;
        else
        $categorySaved = true;

        $response = $this->container->view->render($response, 'view.phtml',['categorySaved'=>$categorySaved]);

        return $response;
    }
}
