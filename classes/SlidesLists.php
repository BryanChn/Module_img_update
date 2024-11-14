<?php

class SlidesLists
{



    public static function renderSlidesList($slides)
    {
        $helper = new HelperList();
        $context = Context::getContext();
        $id_section = (int)Tools::getValue('id_section');

        // Configuration de base
        $helper->show_toolbar = false;
        $helper->simple_header = true;
        $helper->identifier = 'id_slide';
        $helper->table = 's2i_section_slides';
        $helper->actions = ['edit', 'delete'];
        $helper->tpl_vars = [
            'id_section' => $id_section
        ];

        // Configuration pour le drag & drop
        $helper->position_identifier = 'id_slide';
        $helper->orderBy = 'position';
        $helper->orderWay = 'ASC';


        // Autres configurations
        $helper->shopLinkType = '';
        $helper->bootstrap = true;
        $helper->currentIndex = $context->link->getAdminLink('AdminS2iImage', true) . '&id_section=' . $id_section;
        $helper->token = Tools::getAdminTokenLite('AdminS2iImage');

        $helper->base_folder = 'helpers/list/';
        $helper->override_folder = 'extendSlideList/';
        $helper->module = Module::getInstanceByName('s2i_update_img');


        $fields_list = [
            'position' => [
                'title' => 'Position',
                'position' => 'position',
                'align' => 'center',
                'class' => 'fixed-width-sm',
                'orderby' => false
            ],
            'id_slide' => [
                'title' => 'ID',
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ],
            'title' => [
                'title' => 'Titre',
                'align' => 'left'
            ],
            'active' => [
                'title' => 'Actif',
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'class' => 'fixed-width-sm'
            ],
            'image' => [
                'title' => 'Image',
                'align' => 'center',
                'callback' => 'displayImageThumbnail',
                'callback_object' => 'SlidesLists',
                'class' => 'fixed-width-lg'
            ]
        ];

        return $helper->generateList($slides, $fields_list);
    }
    public static function updatePositions($positions, $id_section)
    {
        foreach ($positions as $position) {
            $id_slide = (int)$position['id_slide'];
            $newPosition = (int)$position['position'];

            $result = Db::getInstance()->update(
                's2i_section_slides',
                [
                    'position' => $newPosition
                ],
                'id_slide = ' . $id_slide . ' AND id_section = ' . $id_section
            );

            if (!$result) {
                return false;
            }
        }

        return true;
    }



    public static function displayImageThumbnail($image, $row)
    {
        if (empty($row['id_slide'])) {
            return '--';
        }

        $id_lang = Context::getContext()->language->id;
        $sql = 'SELECT sl.image 
            FROM ' . _DB_PREFIX_ . 's2i_slides_lang sl
            WHERE sl.id_slide = ' . (int)$row['id_slide'] . ' 
            AND sl.id_lang = ' . (int)$id_lang;

        $image = Db::getInstance()->getValue($sql);

        if (empty($image)) {
            return '--';
        }

        $imageUrl = _PS_IMG_ . $image;

        return '<img src="' . $imageUrl . '" alt="" class="img-thumbnail" style="max-width: 200px">';
    }
}
