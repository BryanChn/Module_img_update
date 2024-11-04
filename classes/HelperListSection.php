<?php

class HelperListSection
{
    public static function renderSectionList($module)
    {
        $helper = new HelperList();

        // Configuration de base
        $helper->module = $module;
        $helper->title = 'Liste des sections';
        $helper->table = 's2i_sections';
        $helper->identifier = 'id_s2i_section';
        $helper->shopLinkType = '';
        $helper->simple_header = true;

        // Désactiver la recherche
        $helper->show_toolbar = false;
        $helper->no_link = false;
        $helper->show_filters = false; // Désactive les filtres et la recherche

        // Configuration des URLs
        $helper->currentIndex = Context::getContext()->link->getAdminLink('AdminS2iImage') . '&action=edit';

        $helper->token = Tools::getAdminTokenLite('AdminS2iImage');


        // Configuration des actions   
        $helper->actions = ['edit', 'delete'];


        // Définition des champs
        $fields_list = array(
            'id_s2i_section' => array(
                'title' => 'ID',
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => 'Nom',
                'align' => 'left',
            ),
            'active' => array(
                'title' => 'Actif',
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'class' => 'fixed-width-sm'
            )
        );

        // Récupération des données
        $sections = Db::getInstance()->executeS('
            SELECT id_s2i_section, name, active 
            FROM ' . _DB_PREFIX_ . 's2i_sections
            ORDER BY id_s2i_section ASC
        ');



        return $helper->generateList($sections, $fields_list);
    }
}
