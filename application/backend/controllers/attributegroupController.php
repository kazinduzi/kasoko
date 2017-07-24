<?php
/**
 * Created by PhpStorm.
 * User: Jennie
 * Date: 11-6-2016
 * Time: 21:15
 */
use models\AttributeGroup;


class AttributegroupController extends Admin_controller
{

    /**
     * AttributeGroupController constructor.
     * @param Request $req
     * @param Response $res
     */
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
        $this->title = __('Attribute Group');
        $this->attributeGroups = (new AttributeGroup)->findAll();
        foreach ((new AttributeGroup())->findAll() as $attrGroup) {
            echo count($attrGroup->getAttributes());
        }
    }

    /**
     *
     */
    public function add()
    {
        $this->title = __('add_attributegroup_title');
        if ($this->getRequest()->isPost()) {
            $attributeGroup = new AttributeGroup();
            $attributeGroup->name = $_POST['name'];
            $attributeGroup->required = (integer)$_POST['required'];
            $attributeGroup->save();
            if ('stay' === $_POST['save_mode']) {
                $this->redirect("/admin/attributegroup/edit/{$attributeGroup->getId()}");
            } else {
                $this->redirect('/admin/attributegroup');
            }

        }

    }

    /**
     * @throws Exception
     */
    public function edit()
    {
        $this->attributeGroup = $attributeGroup = new AttributeGroup($this->getArg(0));
        if (!$attributeGroup->getId()) {
            throw new Exception('');
        }
        $this->title = __('edit_attributegroup_title');
        if ($this->getRequest()->isPost()) {
            $attributeGroup->name = $_POST['name'];
            $attributeGroup->required = (integer)$_POST['required'];
            $attributeGroup->save();
            if ('stay' === $_POST['save_mode']) {
                $this->redirect("/admin/attributegroup/edit/{$attributeGroup->getId()}");
            } else {
                $this->redirect('/admin/attributegroup');
            }
        }
    }

    public function view()
    {
        $this->title = __('View attributes');
        $this->attributeGroup = $attributeGroup = new AttributeGroup($this->getArg(0));
        $this->attributes = $attributeGroup->getAttributes();
    }

    /**
     *
     */
    public function addAttribute()
    {
        $this->getTemplate()->setFilename('attributegroup/attribute');
        $this->attributeGroups = (new AttributeGroup())->findAll();
        $attr = new \models\Attribute($this->getArg(0));
        $this->attribute = $attr;
        $this->attributeGroupId = !empty($_GET['attribute_group']) ? (int)$_GET['attribute_group'] : null;
        if ($this->getRequest()->isPost()) {
            $attr = new \models\Attribute();
            $attr->value = $_POST['value'];
            $attr->value_label = $_POST['value_label'];
            $attr->attributegroup_id = $_POST['attributeGroupId'];
            $attr->save();
            $this->redirect("/admin/attributegroup/view/{$attr->attributegroup_id}");
        }
    }

    /**
     *
     */
    public function editAttribute()
    {
        $this->getTemplate()->setFilename('attributegroup/attribute');
        $attr = new \models\Attribute($this->getArg(0));
        $this->attribute = $attr;
        $this->attributeGroupId = $attr->attributegroup_id;
        $this->attributeGroups = (new AttributeGroup())->findAll();
        if ($this->getRequest()->isPost()) {
            $attr->value = $_POST['value'];
            $attr->value_label = $_POST['value_label'];
            $attr->attributegroup_id = $_POST['attributeGroupId'];
            $attr->save();
            $this->redirect("/admin/attributegroup/view/{$attr->attributegroup_id}");
        }
    }
}