<?php

class HelperListSection
{
    public static function renderSectionList($module)
    {
        $helper = new HelperList();
        $context = Context::getContext();

        // Configuration de base
        $helper->module = $module;
        $helper->title = 'Liste des sections';
        $helper->table = 's2i_sections';
        $helper->identifier = 'id_section';
        $helper->shopLinkType = ''; // ou 'shop
        $helper->simple_header = true;
        // Modification du currentIndex pour pointer vers AdminS2iImage
        $helper->currentIndex = $context->link->getAdminLink('AdminS2iImage');
        $helper->token = Tools::getAdminTokenLite('AdminS2iImage');

        $helper->actions = ['edit', 'delete'];


        $fields_list = array(
            'id_section' => array(
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

        $sections = Db::getInstance()->executeS('
            SELECT id_section, name, active 
            FROM ' . _DB_PREFIX_ . 's2i_sections
            ORDER BY id_section ASC
        ');

        return $helper->generateList($sections, $fields_list);
    }
}
