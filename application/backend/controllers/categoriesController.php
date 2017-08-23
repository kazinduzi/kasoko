<?php

class CategoriesController extends Admin_controller
{

    public function __construct(Request $req, Response $res)
    {
        parent::__construct($req, $res);
        $this->Template->setViewSuffix('phtml');
    }

    public function index()
    {
        $template = $this->getTemplate();
        $template->setFilename('categories/overview');
        $template->title = __('Categories');
        $template->categories = Category::getInstance()->getAll();
    }

    public function open()
    {
        $category = new Category($this->getArg());
        $template = $this->getTemplate();
        $template->setFilename('categories/open');
        $template->title = __('Open category');
        $template->category = $category;
        $template->products = $category->getProducts();
    }

    public function add()
    {
        $template = $this->getTemplate();
        $template->setFilename('categories/add');
        $template->title = __('Add new category');
        $template->categories = Category::getInstance()->getAll();
        if ($this->Request->isPost()) {
            try {
                $savemode = $_POST['save_mode'];
                $data = $_POST['category'];
                $category = new Category();
                $category->name = $data['name'];
                $category->seo_name = \Helpers\Stringify::slugify($data['name']);
                $category->description = $data['description'];
                $category->top = 0;
                $category->sort_order = 0;
                $category->meta_keyword = $data['meta_keywords'];
                $category->meta_description = $data['meta_description'];
                $category->image = $data['image'];
                $category->parent_id = (int) $data['parent'];
                $category->status = $data['visible'] ? true : false;
                $category->in_menu = $data['in_menu'] ? true : false;
                $datetime = new DateTime('now');
                $category->date_added = $datetime->format('Y-m-d H:i:s');
                $category->save();
                if ('stay' === $savemode) {
                    $this->redirect('/admin/categories/edit/' . $category->getId());
                } else {
                    $this->redirect('/admin/categories');
                }
            } catch (\Exception $e) {
                print_r($e);
            }
        }
    }

    public function edit()
    {
        $category = new Category($this->getArg());
        $template = $this->getTemplate();
        $template->categories = Category::getInstance()->getAll();
        $template->setFilename('categories/edit');
        $template->title = __('Edit category: ') . $category->name;
        $template->category = $category;
        $template->parentCategory = $category->parent_id ? new Category($category->parent_id) : null;
        if ($this->Request->isPost()) {
            try {
                $data = $_POST['category'];
                $savemode = $_POST['save_mode'];
                $category->name = $data['name'];
                $category->seo_name = \Helpers\Stringify::slugify($data['name']);
                $category->description = $data['description'];
                $category->top = 0;
                $category->sort_order = 0;
                $category->meta_keyword = $data['meta_keywords'];
                $category->meta_description = $data['meta_description'];
                $category->image = $data['image'];
                $category->parent_id = (int) $data['parent'];
                $category->status = $data['visible'] ? true : false;
                $category->in_menu = $data['in_menu'] ? true : false;
                $datetime = new DateTime('now');
                $category->date_modified = $datetime->format('Y-m-d H:i:s');
                $category->save();
                if ('stay' === $savemode) {
                    $this->redirect('/admin/categories/edit/' . $category->getId());
                } else {
                    $this->redirect('/admin/categories');
                }
            } catch (\Exception $e) {
                print_r($e);
            }
        }
    }

    public function delete()
    {
        try {
            $category = new Category($this->getArg(0));
            $category->deleteCategory();
            $this->redirect('/admin/categories');
        } catch (\Exception $e) {
            print_r($e);
        }
    }

}
