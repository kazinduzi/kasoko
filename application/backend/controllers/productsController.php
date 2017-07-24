<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

use library\Image\Editor as ImageEditor;
use models\Manufacturer\Manufacturer;

class ProductsController extends Admin_controller
{

    public function __construct(Request $req, Response $res)
    {
        parent::__construct($req, $res);
        $this->Template->setViewSuffix('phtml');
    }

    /**
     *
     */
    public function index()
    {
        $template = $this->getTemplate();
        $template->setFilename('products/overview');
        $template->title = __('Products overview');
        $template->products = Product::getInstance()->findAll();
    }

    /**
     *
     */
    public function edit()
    {
        $product = new Product($this->getArg());
//        var_dump(count($product->getProductAttributes()));
//        if (count($product->getProductAttributes())) {
//            foreach ($product->getProductAttributes() as $productAttribute) {
//                var_dump(count($productAttribute->getAttributes()));
//            }
//        }
//        $x = (new \models\AttributeGroup())->findAll();
//        print_r($x);
//        die;

        $template = $this->getTemplate();
        $template->setFilename('products/edit');
        $template->title = __('Edit product');
        $template->product = $product;
        $template->allCategories = Category::getInstance()->getAll();
        $template->activeManufacturers = \models\Manufacturer\Manufacturer::getInstance()->getAllActive();
        $template->attributeGroups = (new \models\AttributeGroup())->findAll();
        $template->set('productAttributes', $product->getProductAttributes());

        if ($this->Request->isPost()) {

            $savemode = $_POST['save_mode'];
            $data = $_POST['product'];
            try {
                $product->name = $data['name'];
                $product->model = $data['model'];
                $product->slug = \Stringify::slugify($data['name']);
                $product->description = $data['description'];
                $product->meta_keywords = $data['meta_keywords'];
                $product->meta_description = $data['meta_description'];
                $product->sku = $data['sku'];
                $product->upc = '';
                $product->quantity = (int)$data['quantity'];
                $product->shipping = 1;
                $product->price = $data['price'];
                $product->date_available = $data['date_available'];
                $product->minimum = $data['minimum'];
                $product->sort_order = 0;
                $product->status = $data['visible'];
                $product->viewed = 0;
                $product->date_modified = date('Y-m-d H:i:s');
                $product->setCategories($data['category']);
                $product->setManufacturerId($data['manufacturer']);
                $product->save();

                if ('stay' === $savemode) {
                    $this->redirect('/admin/products/edit/' . $product->getId());
                } else {
                    $this->redirect('/admin/products');
                }
            } catch (\Exception $e) {
                print_r($e);
            }
        }
    }

    public function buildAttributeCombinations()
    {
        $prodAttrId = $this->getArg(0);
        $product_id = $this->getRequest()->getParam('product_id');
        $template = $this->getTemplate();
        $template->setFilename('products/combinations');
        $template->attributeGroups = (new \models\AttributeGroup())->findAll();
        $template->set('product', $product = new Product($product_id));
        $template->set('title','Build product attribute configurations');
        $template->set('productAttributes', $product->getProductAttributes());
        $prodAttr = new \models\Product\AttributeValue($prodAttrId);
        $template->set('productAttributes', $prodAttr);
        if ($this->getRequest()->isPost() && $_POST['product_id'] == $product->getId()) {

            // Save combinations
            $prodAttr->product_id = $_POST['product_id'];
            $prodAttr->price_impact = floatval($_POST['product']['attribute_impact_price']);
            $prodAttr->quantity_impact = floatval($_POST['product']['attribute_impact_quantity']);
            $prodAttr->save();

            if ($prodAttr->getId()) {
                $data_attributes = array_values(array_filter($_POST['product']['attributes']));

                foreach ($data_attributes as $attrId) {
                    $productAttributeConfiguration = new \models\Product\ProductAttributeConfiguration();
                    $productAttributeConfiguration->attribute_id = (int)$attrId;
                    $productAttributeConfiguration->product_id = $_POST['product_id'];
                    $productAttributeConfiguration->product_attributes_id = $prodAttr->getId();
                    $productAttributeConfiguration->save();
                }
            }

            // Redirect
            $this->redirect('/admin/products/edit/'.$product->getId());

        }

    }

    public function deleteAttributeCombinations()
    {
        $prodAttr = new \models\Product\AttributeValue($this->getArg(0));
        $product = new Product($this->getRequest()->getParam('product_id'));
        if ($prodAttr->delete()) {
            $this->redirect('/admin/products/edit/' . $product->getId());
        }
    }


    public function add()
    {
        $template = $this->getTemplate();
        $template->setFilename('products/add');
        $template->title = __('Add new product');
        $template->allCategories = Category::getInstance()->getAll();
        if ($this->Request->isPost()) {
            $savemode = $_POST['save_mode'];
            $data = $_POST['product'];
            try {
                $product = new Product();
                $product->name = $data['name'];
                $product->model = $data['model'];
                $product->slug = \Stringify::slugify($data['name']);
                $product->description = $data['description'];
                $product->meta_keywords = $data['meta_keywords'];
                $product->meta_description = $data['meta_description'];
                $product->sku = $data['sku'];
                $product->upc = '';
                $product->stock_status_id = 1;
                $product->quantity = (int)$data['quantity'];
                $product->shipping = 1;
                $product->price = $data['price'];
                $product->tax = $data['tax'];
                $product->date_available = $data['date_available'];
                $product->minimum = $data['minimum'];
                $product->sort_order = 0;
                $product->status = $data['visible'];
                $product->viewed = 0;
                $product->date_modified = date('Y-m-d H:i:s');
                $product->setCategories($data['category']);
                $product->setManufacturerId($data['manufacturer']);
                $product->save();
                if ('stay' === $savemode) {
                    $this->redirect('/admin/products/edit/' . $product->getId());
                } else {
                    $this->redirect('/admin/products');
                }
            } catch (\Exception $e) {
                print_r($e);
            }
        }
    }

    public function imageList()
    {
        $template = $this->getTemplate();
        $template->setFilename('products/image/list');
        $template->product = new Product($this->getArg());
        echo $template->render();
        exit();
    }

    public function image()
    {
        $product = new Product($this->getArg());
        $targetDirectory = KAZINDUZI_PATH . '/html/images/kasoko/' . Stringify::slugify($product->sku);
        $targetSmallDirectory = KAZINDUZI_PATH . '/html/images/kasoko/' . Stringify::slugify($product->sku) . '/small';
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 777, true);
        }
        if (!is_dir($targetSmallDirectory)) {
            @mkdir($targetSmallDirectory, 777, true);
        }

        foreach ($_FILES as $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                try {
                    $rnd = rand(0, 9999);
                    $targetFilename = $targetDirectory . '/' . Stringify::sanitizeFilename($rnd . '-' . $file['name']);
                    $targetSmallFilename = $targetSmallDirectory . '/' . Stringify::sanitizeFilename($rnd . '-' . $file['name']);
                    $imageFilemanager = ImageEditor::instance();
                    $imageFilemanager->setFile($file['tmp_name']);
                    $imageFilemanager->resize(800, 600);
                    $imageFilemanager->save($targetFilename);
                    $imageFilemanager->resize(200, 150);
                    $imageFilemanager->save($targetSmallFilename);
                    //
                    $productImage = new \library\Product\Image();
                    $productImage->setProduct($product);
                    $productImage->setImage(substr($targetFilename, strlen(KAZINDUZI_PATH)));
                    $productImage->setThumb(substr($targetSmallFilename, strlen(KAZINDUZI_PATH)));
                    $productImage->save();
                } catch (Exception $e) {
                    print_r($e);
                    die();
                }
            }
        }
        json_encode($_FILES);
        die;
    }

    public function updateImage()
    {
        if (!$this->getArg(0) || !$this->getArg(1)) {
            throw new Exception('Unknown productID or imageId');
        }
        $imageProduct = new \library\Product\Image((int)$this->getArg(1));
        $template = $this->getTemplate();
        $template->setFilename('products/image/update');
        $template->title = __('Update product image');
        $template->imageProduct = $imageProduct;
        if ($this->getRequest()->isPost()) {
            $this->proceedUpdateImage($imageProduct, $_POST['product_image_modification']);
        }
    }

    public function deleteImage()
    {
        if (!$this->getArg(0) || !$this->getArg(1)) {
            throw new \Exception('Unknown productID or imageId');
        }
        try {
            $imageProduct = new \library\Product\Image((int)$this->getArg(1));
            $imageProduct->delete();
            $return = array('success' => $imageProduct->getId());
        } catch (Exception $ex) {
            $return = array('error' => $ex->getMessage());
        }
        echo json_encode($return);
        exit();
    }

    protected function proceedUpdateImage(\library\Product\Image $imageProduct, array $data)
    {
        try {
            $imageProduct->setDescription($data['description']);
            $imageProduct->setPostscriptum($data['postscriptum']);
            $imageProduct->setTitle($data['title']);
            $imageProduct->setCover((bool)$data['cover']);
            $imageProduct->save();
            if ('close' === $_POST['save_mode']) {
                $this->redirect('/admin/products/edit/' . $imageProduct->getProduct()->getId());
            }
        } catch (Exception $e) {
            print_r($e);
        }
    }

}
