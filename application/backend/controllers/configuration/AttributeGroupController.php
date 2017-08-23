<?php

use models\AttributeGroup;

class ConfigurationAttributegroupController extends Admin_controller
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
        $this->token = Security::token();
    }

    /**
     * Index action
     */
    public function index()
    {
        $this->title = __('Attribute group');
        $this->attributeGroups = (new AttributeGroup)->findAll();
    }

    /**
     * Add action
     */
    public function add()
    {
        $this->title = __('Add Attributegroup');
        if ($this->getRequest()->isPost() && Security::check($this->getRequest()->postParam('_token'))) {
            $attributeGroup = new AttributeGroup();
            $attributeGroup->name = $this->getRequest()->postParam('name');
            $attributeGroup->required = isset($_POST['required']) ? 1 : 0;
            $attributeGroup->save();
            $this->redirect('/admin/configuration/attributegroup');
        }
    }

    /**
     * Edit action
     */
    public function edit()
    {
        $this->title = __('edit_attributegroup_title');
        if ($this->getRequest()->isPost() && Security::check($this->getRequest()->postParam('_token'))) {
            $attributeGroup = new AttributeGroup($this->getRequest()->postParam('id'));
            $attributeGroup->name = $this->getRequest()->postParam('name');
            $attributeGroup->required = isset($_POST['required']) ? 1 : 0;
            $attributeGroup->save();
            $this->redirect('/admin/configuration/attributegroup');
        }
    }

    /**
     * Delete action
     */
    public function delete()
    {
        $this->title = __('delete_attributegroup_title');
        if ($this->Request->isPost() && Security::check($this->Request->postParam('_token'))) {
            $id = $this->getRequest()->postParam('id');
            $attributeGroup = new AttributeGroup($id);
            $attributeGroup->delete();
            $this->redirect('/admin/configuration/attributegroup');
        }
    }

    /**
     * View action
     */
    public function view()
    {
        $this->title = __('View attributes');
        $this->attributeGroup = $attributeGroup = new AttributeGroup($this->getArg(0));
        $this->attributes = $attributeGroup->getAttributes();
    }

    /**
     * Add attribute action
     */
    public function addAttribute()
    {
        $attributeGroupId = $this->Request->getParam('attribute_group');
        $this->attributeGroup = new AttributeGroup($attributeGroupId);
        if ($this->Request->isPost()) {
            $attribute = new \models\Attribute();
            $attribute->value = $this->Request->postParam('value');
            $attribute->value_label = $this->Request->postParam('value_label');
            $attribute->attributegroup_id = $this->Request->postParam('attributeGroupId');
            $attribute->save();
            $this->redirect("/admin/configuration/attributegroup/view/{$attribute->attributegroup_id}");
        }
        $this->getTemplate()->setFilename('configuration/attributegroup/attribute');
    }

    /**
     * Edit attribute action
     */
    public function editAttribute()
    {
        $this->getTemplate()->setFilename('configuration/attributegroup/attribute');
        $attribute = new \models\Attribute($this->getArg(0));
        $this->attribute = $attribute;
        $this->attributeGroup = new AttributeGroup($attribute->attributegroup_id);
        $this->attributeGroups = (new AttributeGroup())->findAll();
        if ($this->Request->isPost()) {
            $attribute->value = $this->Request->postParam('value');
            $attribute->value_label = $this->Request->postParam('value_label');
            $attribute->attributegroup_id = $this->Request->postParam('attributeGroupId');
            $attribute->save();
            $this->redirect("/admin/configuration/attributegroup/view/{$attribute->attributegroup_id}");
        }
    }

    /**
     * Delete attribute action 
     */
    public function deleteAttribute()
    {
        $attributeId = $this->getArg(0);
        $attribute = new \models\Attribute($attributeId);
        $attributeGroupId = $attribute->AttributeGroup->getId();
        $attribute->delete();
        $this->redirect("/admin/configuration/attributegroup/view/{$attributeGroupId}");
    }

}
