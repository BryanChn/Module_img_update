<?php
class Section extends ObjectModel
{
    public $id_section;
    public $name;
    public $active;
    public $is_slider;
    public $speed;
    public $position;
    public $hook_location;

    public static $definition = [
        'table' => 's2i_sections',
        'primary' => 'id_section',
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'is_slider' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false],
            'speed' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => false],
            'position' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => false],

        ],
    ];

    // gestion des sections par hook 
    public static function getSectionsByHook($hook_name)
    {
        return Db::getInstance()->executeS('
        SELECT * FROM `' . _DB_PREFIX_ . 's2i_sections`
        WHERE `hook_location` = "' . pSQL($hook_name) . '"
        AND `active` = 1
        ORDER BY `position` ASC
    ');
    }
}
