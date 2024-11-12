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
        $helper->position_identifier = 'id_slide';
        $helper->orderBy = 'position';
        $helper->orderWay = 'ASC';

        $helper->bootstrap = true;

        $helper->currentIndex = $context->link->getAdminLink('AdminS2iImage', true);
        $helper->token = Tools::getAdminTokenLite('AdminS2iImage');

        $helper->tpl_vars = [
            'id_section' => $id_section
        ];

        $fields_list = [
            'position' => [
                'title' => 'Position',
                'position' => true,
                'align' => 'center',
                'class' => 'fixed-width-sm',
                'orderby' => true
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
        usort($slides, function ($a, $b) {
            return $a['position'] - $b['position'];
        });
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

        return '<img src="' . $imageUrl . '" alt="" class="img-thumbnail" style="max-width: 200px">';
    }
}
