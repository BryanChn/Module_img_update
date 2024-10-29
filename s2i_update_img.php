<?php
if (!defined('_PS_VERSION_')) {
    exit;
}


require_once _PS_MODULE_DIR_ . 's2i_update_img/classes/S2iLists.php';
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
            && $this->registerHook('displayBackOfficeHeader')
            && $this->createDatabaseTable()
            && $this->addDefaultLists();
    }

    public function hookActionAdminControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            's2i_update_img',
            $this->_path . 'css/style.css'

        );
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    private function createDatabaseTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 's2i_lists` (
            `id_s2i_list` INT(11) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(255) NOT NULL,
            `active` TINYINT(1) NOT NULL DEFAULT 1,
            `slider` TINYINT(1) NOT NULL DEFAULT 0,
            `speed` INT(11)  DEFAULT 5000,
            `image` VARCHAR(255) DEFAULT NULL,
            PRIMARY KEY (`id_s2i_list`),
            UNIQUE KEY `unique_name` (`name`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        return Db::getInstance()->execute($sql);
    }


    public function getLists()
    {
        return S2iLists::getAllLists();
    }


    public function addDefaultLists()
    {
        $defaultLists = [
            ['name' => 'Accueil', 'active' => 1, 'slider' => 0, 'speed' => null, 'image' => null],
            ['name' => 'Footer', 'active' => 1, 'slider' => 0, 'speed' => null, 'image' => null],
            ['name' => 'Header', 'active' => 1, 'slider' => 0, 'speed' => null, 'image' => null],
        ];

        foreach ($defaultLists as $listData) {
            $exists = Db::getInstance()->getValue(
                'SELECT id_s2i_list FROM `' . _DB_PREFIX_ . 's2i_lists` WHERE name = "' . pSQL($listData['name']) . '"'
            );

            if (!$exists) {
                $list = new S2iLists();
                $list->name = $listData['name'];
                $list->active = $listData['active'];
                $list->slider = $listData['slider'];
                $list->speed = $listData['speed'];
                $list->image = $listData['image'];
                $list->add();
            }
        }

        return true;
    }




    public function getContent()
    {
        $lists = $this->getLists();
        $modify_link = $this->context->link->getAdminLink('AdminS2iImage') . '&action=modify&id=';
        $delete_link = $this->context->link->getAdminLink('AdminS2iImage') . '&action=delete&id=';
        $create_link = $this->context->link->getAdminLink('AdminS2iImage') . '&action=create';




        $this->context->smarty->assign([
            'lists' => $lists,
            'modify_link' => $modify_link,
            'delete_link' => $delete_link,
            'create_link' => $create_link,
        ]);

        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configuration.tpl');
    }
}
