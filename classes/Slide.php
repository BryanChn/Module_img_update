<?php
class Slide extends ObjectModel
{
    public $id_slide;
    public $id_section;
    public $active;
    public $position;
    public $only_title;
    public $title_hide;
    public $image_is_mobile;

    public static $definition = [
        'table' => 's2i_section_slides',
        'primary' => 'id_slide',
        'fields' => [
            'id_section' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'position' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => false],
            'only_title' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false],
            'title_hide' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false],
            'image_is_mobile' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false],
        ],
    ];
    public static function getBySection($id_section)
    {
        $sql = 'SELECT ss.*, sl.title, sl.image 
                FROM ' . _DB_PREFIX_ . 's2i_section_slides ss
                LEFT JOIN ' . _DB_PREFIX_ . 's2i_slides_lang sl 
                ON ss.id_slide = sl.id_slide 
                AND sl.id_lang = ' . (int)Context::getContext()->language->id . '
                WHERE ss.id_section = ' . (int)$id_section . '
                ORDER BY ss.position ASC';
        return Db::getInstance()->executeS($sql);
    }
}
