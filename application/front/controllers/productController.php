<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

/**
 * @author Emmanuel_Leonie
 */
class ProductController extends BaseController
{

	/**
	 *
	 * @var array
	 */
	protected $scripts = array();
	protected $styles = array();

	/**
	 * Constructor
	 *
	 * @param Request $req
	 * @param Response $res
	 */
	public function __construct(Request $req = null, Response $res = null)
	{
		parent::__construct($req, $res);
		$this->Template->setViewSuffix('phtml');
	}

	/**
	 * indexAction
	 */
	public function index()
	{
		return $this->viewAll();
	}

	/**
	 * View product
	 */
	public function item()
	{
		$sku = $this->getArg();
		$product = current(Product::getSpecialBySlug($sku));
		$this->Template->setViewSuffix('phtml');
		$this->Template->setFilename('product/index');
		$this->Template->meta_keywords = $product->meta_keywords;
		$this->Template->meta_description = $product->meta_description;
		$this->Template->product = $product;
		$this->Template->images = $product->getProductImages();
		$this->Template->product_manufacturer = $product->getManufacturer();

		$productAttributeConfigurations = $product->getProductAttributeConfigurations();
		$prodAttrsConfigurationsIteraror = new \Helpers\Iterators\ProductAttributeConfigurationsIterator(
				new ArrayIterator($productAttributeConfigurations)
		);
		$this->associatedAttributesWithGroups = $prodAttrsConfigurationsIteraror->getAssociatedAttributesWithGroups();

		// Render the options for product
		$optionsTemplate = new Template('product/options', Template::PHTML_EXTENSTION);
		$optionsTemplate->product = $product;
		$optionsTemplate->associatedAttributesWithGroups = $prodAttrsConfigurationsIteraror->getAssociatedAttributesWithGroups();
		$this->optionsContent = $optionsTemplate->render();

		//$product->updateViewed();
		// Append JS Scripts
		$this->addJsFiles(
				array(
						'/_theme/default/front/js/vendor/jquery.elevatezoom.min.js',
						'/_theme/default/front/js/vendor/jquery.fancybox.pack.js',
						'/_theme/default/front/js/product_elevate_zoom.js',
				)
		);
	}

	/**
	 *
	 */
	public function view()
	{
		$productId = $this->getArg(0);
		$product = new Product($productId);
		$this->Template->setViewSuffix('phtml');
		$this->Template->setFilename('product/index');
		$this->Template->meta_keywords = $product->meta_keywords;
		$this->Template->meta_description = $product->meta_description;
		$this->Template->product = $product;
		$this->Template->images = $product->getProductImages();
		$this->Template->product_manufacturer = $product->getManufacturer();
		$this->attributeGroups = (new \models\AttributeGroup())->findAll();

		//$product->updateViewed();
		$this->addJsFiles(
				array(
					'/_theme/default/front/js/vendor/jquery.elevatezoom.min.js',
					'/_theme/default/front/js/vendor/jquery.fancybox.pack.js',
					'/_theme/default/front/js/product_elevate_zoom.js',
				)
		);
	}

	/**
	 *
	 */
	public function special()
	{
		$slug = $this->getArg();
		$this->Template->setViewSuffix('phtml');
		$this->Template->setFilename('product/special');
		$specialProduct = current(Product::getSpecialBySlug($slug));
		if (!empty($specialProduct)) {
			$this->Template->product = $specialProduct;
			$this->Template->images = $specialProduct->getProductImages();
			$this->Template->product_manufacturer = $specialProduct->getManufacturer();
		} else {
			redirect('/');
		}

		$this->addJsFiles(
				array(
						'/_theme/default/front/js/vendor/jquery.elevatezoom.min.js',
						'/_theme/default/front/js/vendor/jquery.fancybox.pack.js',
						'/_theme/default/front/js/product_elevate_zoom.js',
				)
		);
	}

	/**
	 * Compare the product
	 */
	public function compare()
	{

	}

	/**
	 * View all products
	 *
	 * @return void
	 */
	public function viewAll()
	{
		$template = $this->getTemplate()->setViewSuffix('phtml');
		$type = $this->getRequest()->getParam('type');
		$limit = $this->getRequest()->getParam('limit');
		$limit = !empty($limit) ? $limit : 8;
		$order = $this->getRequest()->getParam('order');
		$page = $this->getRequest()->getParam('page');
		$page = !empty($page) ? $page : 0;

		$template->title = __('View all');
		$template->offset = $page * $limit;
		$template->limit = $limit;
		$template->order = $order;
		$template->type = $type;
		switch ($type) {
			case 'new':
			default :
				$template->setFilename('product/view_all');
				$template->title = __('All products');
				$template->products = $products = \Product::getAll(array('limit' => $limit, 'order' => $order, 'type' => $type));
				break;
			case 'offers':
				$template->setFilename('product/view_all');
				$template->title = __('Sale products');
				$template->products = '';
				break;
			case 'special':
				$template->setFilename('product/view_all');
				$template->title = __('Special products');
				$template->products = '';
		}
		$template->limitOptions = array(
				'4' => 4,
				'8' => 8,
				'12' => 12,
				'50' => 50,
				'All' => 100000
		);
		$template->sortOptions = array(
				\Category::SORT_ALPHA => __('Name A to Z'),
				\Category::SORT_ALPHA_REV => __('Name Z to A'),
				\Category::SORT_PRICE_MIN => __('Price Lowest'),
				\Category::SORT_PRICE_MAX => __('Price Highest')
		);

		// Pagination
		$paginateTemplate = new Template('product/pagination', 'phtml');
		$paginateTemplate->total = iterator_count($products);
		$paginateTemplate->page = $page;
		$paginateTemplate->limit = $limit;
		$paginateTemplate->order = $order;
		$template->paginationHtml = $paginateTemplate->render();
	}

	protected function updateViewed(Product $product)
	{
		$product->updateViewed();
	}

}
