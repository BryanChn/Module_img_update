<?php
if (!defined('_PS_VERSION_')) {
    exit;
}


require_once _PS_MODULE_DIR_ . 's2i_update_img/classes/Section.php';
require_once _PS_MODULE_DIR_ . 's2i_update_img/classes/HelperListSection.php';
require_once _PS_MODULE_DIR_ . 's2i_update_img/classes/Create_section_form.php';
require_once _PS_MODULE_DIR_ . 's2i_update_img/controllers/admin/AdminS2iImageController.php';
require_once _PS_MODULE_DIR_ . 's2i_update_img/classes/HelperEditSection.php';
require_once dirname(__FILE__) . '/../../config/config.inc.php';



class S2i_Update_Img extends Module
{
    public function __construct()
    {
        $this->name = 's2i_update_img';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Votre nom';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('S2I Update Image', [], 'Modules.S2iUpdateImg.Admin');
        $this->description = $this->trans('Changez vos photos via notre interface', [], 'Modules.S2iUpdateImg.Admin');
    }


    public function install()
    {
        // Ajout de l'installation du controller admin
        if (
            !parent::install()
            || !$this->registerHook('hookActionAdminControllerSetMedia')
            || !$this->registerHook('hookDisplayBackOfficeHeader')
            || !$this->createDatabaseTable()
            || !$this->insertDefaultSection()
            || !$this->installTab()  // Nouvelle ligne
        ) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        // Ajout de la désinstallation du controller admin
        $this->uninstallTab();  // Nouvelle ligne
        return parent::uninstall();
    }

    private function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminS2iImage';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'S2i Image';
        }

        // Récupération de l'ID parent via requête directe
        $parentTabID = (int)Db::getInstance()->getValue(
            '
            SELECT id_tab 
            FROM `' . _DB_PREFIX_ . 'tab` 
            WHERE class_name = "AdminParentModules"'
        );

        $tab->id_parent = $parentTabID;
        $tab->module = $this->name;

        return $tab->add();
    }

    private function uninstallTab()
    {
        $id_tab = (int)Db::getInstance()->getValue(
            '
            SELECT id_tab 
            FROM `' . _DB_PREFIX_ . 'tab` 
            WHERE class_name = "AdminS2iImage"'
        );

        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }
        return true;
    }



    protected function createDatabaseTable()
    {
        $sql = [];

        // Table principale des sections
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 's2i_sections` (
            `id_s2i_section` INT(11) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(255) NOT NULL,
            `active` TINYINT(1) NOT NULL DEFAULT 1,
            `slider` TINYINT(1) NOT NULL DEFAULT 0,
            `speed` INT(11) NOT NULL DEFAULT 5000,
            PRIMARY KEY (`id_s2i_section`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        // Table des détails des sections
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 's2i_section_details` (
            `id_s2i_detail` INT(11) NOT NULL AUTO_INCREMENT,
            `id_s2i_section` INT(11) NOT NULL,
            `active` TINYINT(1) NOT NULL DEFAULT 1,
            `position` INT(10) unsigned NOT NULL DEFAULT 0,
            `only_title` TINYINT(1) NOT NULL DEFAULT 0,         
            PRIMARY KEY (`id_s2i_detail`),
            FOREIGN KEY (`id_s2i_section`) REFERENCES `' . _DB_PREFIX_ . 's2i_sections`(`id_s2i_section`) ON DELETE CASCADE
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        // Table des traductions pour les détails
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 's2i_section_details_lang` (
            `id_s2i_detail_lang` INT(11) NOT NULL AUTO_INCREMENT,
            `id_s2i_detail` INT(11) NOT NULL,
            `id_lang` INT(11) NOT NULL,
            `title` VARCHAR(255) NOT NULL,
            `image_is_mobile` TINYINT(1) NOT NULL DEFAULT 0,
            `legend` VARCHAR(255) NULL,
            `url` VARCHAR(255) NULL,
            `image` VARCHAR(255) NULL,
            `image_mobile` VARCHAR(255) NULL,
            PRIMARY KEY (`id_s2i_detail_lang`),
            UNIQUE KEY `id_s2i_detail_lang_unique` (`id_s2i_detail`, `id_lang`),
            FOREIGN KEY (`id_s2i_detail`) REFERENCES `' . _DB_PREFIX_ . 's2i_section_details`(`id_s2i_detail`) ON DELETE CASCADE
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';


        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }
    protected function insertDefaultSection()
    {
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 's2i_sections` (`name`, `active`, `slider`, `speed`)
            VALUES ("Accueil", 1, 0, 5000)';

        return Db::getInstance()->execute($sql);
    }

    public function getSection()
    {
        return HelperListSection::renderSectionList($this, context::getContext());
    }

    public function getContent()

    {
        $form = new Create_section_form($this);
        $section_form = $form->renderForm();
        $sectionsList = $this->getSection();

        $success_message = $this->context->cookie->__get('s2i_success_message');
        if ($success_message) {
            $this->context->controller->confirmations[] = $this->trans($success_message);
            $this->context->cookie->__unset('s2i_success_message');
            $this->context->cookie->write();
        }

        $this->context->smarty->assign([
            'section_form' => $section_form,
            'sectionsList' => $sectionsList,
            'errors' => $this->context->controller->errors,
            'confirmations' => $this->context->controller->confirmations,
        ]);


        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configuration.tpl');
    }
}
