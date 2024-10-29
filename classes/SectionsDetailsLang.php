<?php
class SectionDetailsLang extends ObjectModel
{
    public $id_s2i_detail;
    public $id_lang;
    public $title;
    public $legend;
    public $url;
    public $image;

    public static $definition = [
        'table' => 's2i_section_details_lang',
        'primary' => 'id_s2i_detail',
        'multilang' => true,
        'fields' => [
            'id_s2i_detail' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'id_lang' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
            'legend' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255],
            'url' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isUrl', 'size' => 255],
            'image' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isFileName', 'size' => 255],
        ],
    ];
}
