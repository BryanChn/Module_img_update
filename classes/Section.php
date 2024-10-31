<?php
class Sections extends ObjectModel
{
    public $id_s2i_section;
    public $name;
    public $active;
    public $slider;
    public $speed;

    public static $definition = [
        'table' => 's2i_sections',
        'primary' => 'id_s2i_section',
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'slider' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false],
            'speed' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => false],
        ],
    ];
}
