<?php
class SlideLang extends ObjectModel
{
    public $id_slide_lang;
    public $id_slide;
    public $id_lang;
    public $title;
    public $legend;
    public $url;
    public $image;
    public $image_mobile;

    public static $definition = [
        'table' => 's2i_slides_lang',
        'primary' => 'id_slide_lang',
        'fields' => [
            'id_slide' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'id_lang' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'title' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
            'legend' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false, 'size' => 255],
            'url' => ['type' => self::TYPE_STRING, 'validate' => 'isUrl', 'required' => false, 'size' => 255],
            'image' => ['type' => self::TYPE_STRING, 'validate' => 'isAnything', 'required' => false, 'size' => 255],
            'image_mobile' => ['type' => self::TYPE_STRING, 'validate' => 'isAnything', 'required' => false, 'size' => 255],
        ],
    ];

    public static function getBySlideAndLang($id_slide, $id_lang)
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 's2i_slides_lang 
                WHERE id_slide = ' . (int)$id_slide . ' 
                AND id_lang = ' . (int)$id_lang;
        return Db::getInstance()->getRow($sql);
    }
}
