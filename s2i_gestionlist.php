<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 's2i_gestionlist/classes/SlidesLists.php';
require_once _PS_MODULE_DIR_ . 's2i_gestionlist/classes/Section.php';
require_once _PS_MODULE_DIR_ . 's2i_gestionlist/classes/Slide.php';
require_once _PS_MODULE_DIR_ . 's2i_gestionlist/classes/SlideLang.php';
require_once _PS_MODULE_DIR_ . 's2i_gestionlist/classes/HelperListSection.php';
require_once _PS_MODULE_DIR_ . 's2i_gestionlist/classes/Create_section_form.php';
require_once _PS_MODULE_DIR_ . 's2i_gestionlist/classes/HelperEditSection.php';
require_once _PS_MODULE_DIR_ . 's2i_gestionlist/classes/SlideManager.php';
require_once _PS_MODULE_DIR_ . 's2i_gestionlist/classes/HookLocation.php';
require_once dirname(__FILE__) . '/../../config/config.inc.php';



class S2i_gestionlist extends Module
{
    public function __construct()
    {
        $this->name = 's2i_gestionlist';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'S2i Evolution';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->trans('S2I Gestion List', [], 'Modules.S2iGestionList.Admin');
        $this->description = $this->trans('Gerez vos listes de sections et slides !', [], 'Modules.S2iGestionList.Admin');
    }
    public function install()
    {
        return parent::install()

            && $this->createDatabaseTable()
            && $this->installTab()
            // ajouter les différents hooks voulus
            && $this->registerHook('displayFooter')
            && $this->registerHook('displaySlideTitle')
            && $this->insertDefaultSection();
    }
    private function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminS2iGestionlist';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'S2i Gestion List';
        }
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

    protected function createDatabaseTable()
    {
        $sql = [];

        // Table principale des sections
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 's2i_sections` (
            `id_section` INT(11) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(255) NOT NULL,
            `active` TINYINT(1) NOT NULL DEFAULT 1,
            `is_slider` TINYINT(1) NOT NULL DEFAULT 0,
            `speed` INT(11) NOT NULL DEFAULT 5000,
            `position` INT(10) unsigned NOT NULL DEFAULT 0,         
            PRIMARY KEY (`id_section`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        // Table des hooks des sections
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 's2i_section_hooks` (
            `id_section_hook` INT AUTO_INCREMENT PRIMARY KEY,
            `id_section` INT NOT NULL,
            `hook_name` VARCHAR(255) NOT NULL,          
            FOREIGN KEY (`id_section`) REFERENCES `' . _DB_PREFIX_ . 's2i_sections`(`id_section`) ON DELETE CASCADE
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        // Table des détails des sections
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 's2i_section_slides` (
            `id_slide` INT(11) NOT NULL AUTO_INCREMENT,
            `id_section` INT(11) NOT NULL,
            `active` TINYINT(1) NOT NULL DEFAULT 1,
            `position` INT(10) unsigned NOT NULL DEFAULT 1,
            `display_datePicker` TINYINT(1) NOT NULL DEFAULT 0,
            `start_date` DATETIME NULL,
            `end_date` DATETIME NULL,
            `only_title` TINYINT(1) NOT NULL DEFAULT 0,
            `title_hide` TINYINT(1) NOT NULL DEFAULT 0,
            `image_is_mobile` TINYINT(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id_slide`),
            FOREIGN KEY (`id_section`) REFERENCES `' . _DB_PREFIX_ . 's2i_sections`(`id_section`) ON DELETE CASCADE
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
        // Table des traductions pour les détails
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 's2i_slides_lang` (
          `id_slide_lang` INT(11) NOT NULL AUTO_INCREMENT,
          `id_slide` INT(11) NOT NULL,
          `id_lang` INT(11) NOT NULL,
          `title` VARCHAR(255) NOT NULL,
          `legend` VARCHAR(255) NULL,
          `url` VARCHAR(255) NULL,
          `image` VARCHAR(255) NULL,
          `image_mobile` VARCHAR(255) NULL,
          PRIMARY KEY (`id_slide_lang`),
          UNIQUE KEY `slide_lang_unique` (`id_slide`, `id_lang`),
            FOREIGN KEY (`id_slide`) REFERENCES `' . _DB_PREFIX_ . 's2i_section_slides`(`id_slide`) ON DELETE CASCADE
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';


        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }
        return true;
    }


    public function uninstall()
    {
        $this->uninstallTab();
        $this->uninstallDatabaseTable();
        return parent::uninstall();
    }




    private function uninstallDatabaseTable()
    {
        $sql = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 's2i_sections`, `' . _DB_PREFIX_ . 's2i_section_hooks`, `' . _DB_PREFIX_ . 's2i_section_slides`, `' . _DB_PREFIX_ . 's2i_slides_lang`';
        return Db::getInstance()->execute($sql);
    }
    private function uninstallTab()
    {
        $id_tab = (int)Db::getInstance()->getValue(
            '
            SELECT id_tab 
            FROM `' . _DB_PREFIX_ . 'tab` 
            WHERE class_name = "AdminS2iGestionlist"'
        );

        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }
        return true;
    }




    protected function insertDefaultSection()
    {
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 's2i_sections` (`name`, `active`, `is_slider`, `speed`, `position`)
            VALUES ("Accueil", 1, 0, 5000, 0)';

        return Db::getInstance()->execute($sql);
    }

    public function getSection()
    {
        return HelperListSection::renderSectionList($this);
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

    // partie gestion hooks
    private function displaySlidesForHook($hookName)
    {
        // Récupère les sections liées au hook spécifié
        $sections = HookLocation::getSectionsByHook($hookName);

        $allSlides = [];
        foreach ($sections as $section) {

            $slides = SlidesLists::getSlidesList($section['id_section']);

            // Filtre les slides actifs
            $filteredSlides = array_filter($slides, function ($slide) use ($hookName) {
                $isActive = $slide['active'];

                // verif de only_title
                if ($hookName === 'displaySlideTitle') {
                    return $isActive && $slide['only_title'];
                }

                return $isActive;
            });

            if (!empty($filteredSlides)) {
                // Ajoute les informations de la section aux slides
                foreach ($filteredSlides as &$slide) {
                    $slide['is_slider'] = $section['is_slider'];
                    $slide['speed'] = $section['speed'];
                }
                $allSlides = array_merge($allSlides, $filteredSlides);
            }
        }
        usort($allSlides, function ($a, $b) {
            return $a['position'] - $b['position'];
        });

        $this->context->smarty->assign([
            'slides' => $allSlides,
            'hook_name' => $hookName
        ]);

        // Sélectionne le template approprié selon le hook && ajoutez ici les templates voulus
        $template = 'default-slides.tpl';
        if ($hookName === 'displaySlideTitle') {
            $template = 'search-menu.tpl';
        }


        return $this->display(__FILE__, 'views/templates/hook/' . $template);
    }
    public function hookDisplaySlideTitle()
    {
        return $this->displaySlidesForHook('displaySlideTitle');
    }

    public function hookDisplayFooter()
    {
        return $this->displaySlidesForHook('displayFooter');
    }
}
