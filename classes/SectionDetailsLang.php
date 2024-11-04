<?php
class SectionDetailsLang extends ObjectModel
{
    public $id_s2i_detail_lang; // Ajout d'une clé primaire auto-incrémentée
    public $id_s2i_detail;
    public $id_lang;
    public $title;
    public $legend;
    public $url;
    public $image;
    public $image_is_mobile;
    public $image_mobile;

    public static $definition = [
        'table' => 's2i_section_details_lang',
        'primary' => 'id_s2i_detail_lang',
        'fields' => [
            'id_s2i_detail_lang' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'primary' => true],
            'id_s2i_detail' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'id_lang' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'title' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
            'legend' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255],
            'url' => ['type' => self::TYPE_STRING, 'validate' => 'isUrl', 'size' => 255],
            'image' => ['type' => self::TYPE_STRING, 'validate' => 'isFileName', 'size' => 255],
            'image_mobile' => ['type' => self::TYPE_STRING, 'validate' => 'isFileName', 'size' => 255],
            'image_is_mobile' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false],
        ],
    ];

    public static function getByDetailAndLang($id_detail, $id_lang)
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 's2i_section_details_lang WHERE id_s2i_detail = ' . (int)$id_detail . ' AND id_lang = ' . (int)$id_lang;
        return Db::getInstance()->getRow($sql);
    }
}
