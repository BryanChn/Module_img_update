<?php



class HelperListSection
{
    public static function getAllSections()
    {
        $sql = 'SELECT id_s2i_section, name, active FROM ' . _DB_PREFIX_ . 's2i_sections';
        return Db::getInstance()->executeS($sql);
    }
    public static function renderSectionList($module)

    {

        // Création de l'instance HelperList
        $helper = new HelperList();
        $helper->module = $module;
        $helper->title = 'Liste des sections';
        $helper->table = 's2i_sections';
        $helper->identifier = 'id_s2i_section';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->shopLinkType = '';
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $module->name;
        $helper->actions = ['edit', 'delete'];
        $helper->simple_header = true;

        // Définition des champs pour la liste
        $fields_list = [
            'id_s2i_section' => [
                'title' => 'ID',
                'type' => 'text'
            ],
            'name' => [
                'title' => 'Nom',
                'type' => 'text'
            ],
            'active' => [
                'title' => 'Actif',
                'type' => 'bool',
                'active' => 'status',
            ],
        ];


        // Récupération des données de sections
        $data = self::getAllSections();

        // Vérification de la structure des données
        if (!is_array($data) || !is_array(reset($data))) {
            die('Les données récupérées ne sont pas structurées comme prévu.');
        }

        // Génération de la liste
        return $helper->generateList($data, $fields_list);
    }
}
