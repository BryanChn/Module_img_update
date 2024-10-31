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
        return parent::install()
            && $this->registerHook('hookActionAdminControllerSetMedia')
            && $this->registerHook('hookDisplayBackOfficeHeader')
            && $this->createDatabaseTable()
            && $this->insertDefaultSection();
    }

    public function hookActionAdminControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            's2i_update_img',
            $this->_path . 'css/style.css'
        );
        $this->context->controller->registerJavascript(
            'bootstrap_bundle',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'
        );
    }
    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->registerJavascript(
            's2i_update_img',
            $this->_path . 'js/form_hide.js'
        );
    }

    public function uninstall()
    {
        return parent::uninstall();
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
            `only_title` TINYINT(1) NOT NULL DEFAULT 0,
            `image_is_mobile` INT(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id_s2i_detail`),
            FOREIGN KEY (`id_s2i_section`) REFERENCES `' . _DB_PREFIX_ . 's2i_sections`(`id_s2i_section`) ON DELETE CASCADE
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        // Table des traductions pour les détails
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 's2i_section_details_lang` (
            `id_s2i_detail` INT(11) NOT NULL,
            `id_lang` INT(11) NOT NULL,
            `title` VARCHAR(255) NOT NULL,
            `legend` VARCHAR(255) NULL,
            `url` VARCHAR(255) NULL,
            `image` VARCHAR(255) NULL,
            PRIMARY KEY (`id_s2i_detail`, `id_lang`),
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
        return HelperListSection::renderSectionList($this);
    }





    public function getContent()
    {

        if (Tools::isSubmit('submit_create_section')) {
            $controller = new AdminS2iImageController();
            $controller->postProcess();
        }

        // redirection lorsque l'on clique sur edit dans la liste des sections
        if (Tools::getValue('action') === 'edit' && Tools::getValue('id_s2i_section')) {
            $id_s2i_section = (int)Tools::getValue('id_s2i_section');
            $editForm = HelperEditSection::renderEditForm($this, $id_s2i_section);

            // Assigner le formulaire généré à Smarty
            $this->context->smarty->assign([
                'editForm' => $editForm,
            ]);

            // Charger le template d'édition
            return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/edit_section.tpl');
        }


        $form = new Create_section_form($this);
        $section_form = $form->renderForm();
        $sectionsList = $this->getSection();





        $this->context->smarty->assign([
            'section_form' => $section_form,
            'sectionsList' => $sectionsList,

        ]);

        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configuration.tpl');
    }
}
