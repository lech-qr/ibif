<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class HomepageBoxes extends Module
{
    public function __construct()
    {
        $this->name = 'homepageboxes';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'QR';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Homepage Boxes for IBIF');
        $this->description = $this->l('Recruitment task.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        if (!parent::install() ||
            !$this->installDatabase() ||
            !$this->registerHook('displayHome') ||
            !$this->installTab('AdminHomepageBoxes', 'Homepage Boxes')
        ) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
            !$this->uninstallDatabase() ||
            !$this->uninstallTab('AdminHomepageBoxes') ||
            !$this->executeSqlScript('uninstall.sql')
        ) {
            return false;
        }

        return true;
    }

    private function installDatabase()
    {
        // Get SQL content
        $sql_file = dirname(__FILE__).'/sql/install.sql';
        $sql_content = Tools::file_get_contents($sql_file);
        $sql_requests = str_replace('PREFIX_', _DB_PREFIX_, $sql_content);

        // Execute SQL requests
        $sql_requests = preg_split("/;\s*[\r\n]+/", $sql_requests);
        foreach ($sql_requests as $sql) {
            if (!empty($sql) && !Db::getInstance()->execute(trim($sql))) {
                return false;
            }
        }

        return true;
    }
    private function uninstallDatabase()
    {
        $sql = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'homepageboxes`;';

        if (!Db::getInstance()->execute($sql)) {
            return false;
        }

        return true;
    }    

    private function installTab($className, $name)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $name;
        }
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentModulesSf');
        $tab->module = $this->name;
        return $tab->add();
    }

    private function uninstallTab($className)
    {
        $id_tab = (int) Tab::getIdFromClassName($className);
        $tab = new Tab($id_tab);
        return $tab->delete();
    }

    private function executeSqlScript($file)
    {
        $file = dirname(__FILE__) . '/sql/' . $file;
        if (!file_exists($file)) {
            return false;
        }

        $sql = file_get_contents($file);
        $queries = preg_split('/;\s*[\r\n]+/', trim($sql));

        foreach ($queries as $query) {
            if (!Db::getInstance()->execute(trim($query))) {
                return false;
            }
        }

        return true;
    }

    public function getContent()
    {
        $output = null;
    
        if (Tools::isSubmit('submit' . $this->name)) {
    
            $boxTitle = Tools::getValue('box_title');
            $link = Tools::getValue('box_link');
            $backgroundImage = '';
    
            // Handle file upload
            if (isset($_FILES['box_background_image']) && $_FILES['box_background_image']['error'] == UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['box_background_image']['tmp_name'];
                $fileName = $_FILES['box_background_image']['name'];
                $fileSize = $_FILES['box_background_image']['size'];
                $fileType = $_FILES['box_background_image']['type'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));
                $allowedfileExtensions = array('webp', 'svg', 'jpg', 'gif', 'png', 'jpeg');
    
                if (in_array($fileExtension, $allowedfileExtensions)) {
                    $uploadFileDir = _PS_MODULE_DIR_ . 'homepageboxes/uploads/';
                    $dest_path = $uploadFileDir . $fileName;
    
                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $backgroundImage = 'modules/homepageboxes/uploads/' . $fileName;
                    } else {
                        $output .= $this->displayError($this->trans('There was some error moving the file to upload directory.'));
                    }
                } else {
                    $output .= $this->displayError($this->trans('Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions)));
                }
            }
    
            // Insert into database
            if (!empty($boxTitle) && !empty($link)) {
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'homepageboxes (title, background_image, link) VALUES ("' . pSQL($boxTitle) . '", "' . pSQL($backgroundImage) . '", "' . pSQL($link) . '")';
                if (Db::getInstance()->execute($sql)) {
                    $output .= $this->displayConfirmation($this->trans('Settings updated successfully.'));
                } else {
                    $output .= $this->displayError($this->trans('Failed to save data to the database.'));
                }
            } else {
                $output .= $this->displayError($this->trans('Please fill all fields.'));
            }
        }
    
        return $output . $this->renderForm();
    }
    
    

    private function processForm()
    {
        $boxes = Tools::getValue('HOMEPAGEBOXES');
        $boxes = json_decode($boxes, true);

        Db::getInstance()->execute('TRUNCATE TABLE ' . _DB_PREFIX_ . 'homepageboxes');
        foreach ($boxes as $box) {
            Db::getInstance()->insert('homepageboxes', array(
                'title' => pSQL($box['title']),
                'background_image' => pSQL($box['background_image']),
            ));
        }
    }

    public function renderForm()
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
            'name' => $this->name,
            'token' => Tools::getAdminTokenLite('AdminModules')
        ));
    
        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');
    }    

    public function hookDisplayHome($params)
    {

        $this->context->controller->addJS($this->_path.'js/script.min.js');
        $boxes = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'homepageboxes ORDER BY id_box DESC');
    
        $this->context->smarty->assign(array(
            'boxes' => $boxes !== false ? $boxes : array(),
        ));
    
        $this->context->controller->addCSS($this->_path . 'views/css/homepageboxes.css');
    
        return $this->display(__FILE__, 'homepageboxes.tpl');
    }
    
    
}

