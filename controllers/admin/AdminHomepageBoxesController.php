<?php
class AdminHomepageBoxesController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign(array(
            'currentIndex' => self::$currentIndex,
            'name' => $this->module->name,
            'token' => Tools::getAdminTokenLite('AdminModules'),
            'boxes_json' => Tools::jsonEncode($this->getBoxes())
        ));

        //$this->setTemplate('module:homepageboxes/views/templates/admin/configure.tpl');
        $this->setTemplate('configure.tpl');
    }

    private function getBoxes()
    {
        $boxes = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'homepageboxes ORDER BY id_box DESC');
        $boxes_json = json_encode($boxes);
    
        $categories = Category::getCategories($this->context->language->id, true, false);
        $products = Product::getProducts((int) Context::getContext()->language->id, 0, 0, 'name', 'asc');
        $cmsPages = CMS::listCms((int) Context::getContext()->language->id);
    
        $this->context->smarty->assign(array(
            'boxes_json' => $boxes_json,
            'categories' => json_encode($categories),
            'products' => json_encode($products),
            'cms_pages' => json_encode($cmsPages),
            'currentIndex' => AdminController::$currentIndex,
            //'name' => $this->name,
            'token' => Tools::getAdminTokenLite('AdminModules')
        ));
    
        $this->setTemplate('configure.tpl');
    }  
}
