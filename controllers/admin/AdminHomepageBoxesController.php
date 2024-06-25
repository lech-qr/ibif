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

        // $this->setTemplate('module:homepageboxes/views/templates/admin/configure.tpl');
        $this->setTemplate('configure.tpl');
    }

    private function getBoxes()
    {
        return Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'homepageboxes');
    }
}
