<?php

class SlidesLists
{


    public static function renderSlidesList($slides)
    {
        $helper = new HelperList();
        $context = Context::getContext();
        $id_section = (int)Tools::getValue('id_section');

        $helper->show_toolbar = false;
        $helper->simple_header = true;
        $helper->identifier = 'id_slide';
        $helper->table = 's2i_section_slides';
        $helper->actions = ['edit', 'delete'];
        $helper->shopLinkType = '';
        $helper->bootstrap = true;
        $helper->currentIndex = $context->link->getAdminLink('AdminS2iImage', true) . '&id_section=' . $id_section;
        $helper->token = Tools::getAdminTokenLite('AdminS2iImage');
        // $helper->override_folder = 'extendSlideList/'; 
        $context = Context::getContext();


        // Configuration pour le drag & drop
        $helper->position_identifier = 'id_slide';
        $helper->orderBy = 'position';
        $helper->orderWay = 'ASC';




        $fields_list = [
            'position' => [
                'title' => 'Position',
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'position' => 'position',
                'orderby' => false
            ],
            'id_slide' => [
                'title' => 'ID',
                'align' => 'center',

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

            ],
            'image' => [
                'title' => 'Image',
                'align' => 'center',
                'callback' => 'displayImageThumbnail',
                'callback_object' => 'SlidesLists',

            ]
        ];

        return $helper->generateList($slides, $fields_list);
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

        return '<img src="' . $imageUrl . '" alt="" class="img-thumbnail" style="max-width: 90px">';
    }
    public static function getSlidesList($id_section)
    {
        $context = Context::getContext();
        $id_lang = $context->language->id;

        $sql = 'SELECT ss.*, sl.title, sl.legend, sl.url
        FROM ' . _DB_PREFIX_ . 's2i_section_slides ss
        LEFT JOIN ' . _DB_PREFIX_ . 's2i_slides_lang sl 
        ON ss.id_slide = sl.id_slide 
        AND sl.id_lang = ' . (int)$id_lang . '
        WHERE ss.id_section = ' . (int)$id_section . '
        ORDER BY ss.position ASC';

        return Db::getInstance()->executeS($sql);
    }
}
