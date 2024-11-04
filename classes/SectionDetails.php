<?php
class SectionDetails extends ObjectModel
{
    public $id_s2i_detail;
    public $id_s2i_section;
    public $active;
    public $only_title;
    public $position;

    public static $definition = [
        'table' => 's2i_section_details',
        'primary' => 'id_s2i_detail',
        'fields' => [
            'id_s2i_section' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'only_title' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false],
            'position' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => false],
        ],
    ];

    public static function getBySectionId($id_section)
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 's2i_section_details WHERE id_s2i_section = ' . (int)$id_section;
        return Db::getInstance()->getRow($sql);
    }
}
