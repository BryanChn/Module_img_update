<?php

class S2iLists extends ObjectModel
{
    public $name;
    public $active;
    public $slider;
    public $speed;
    public $image;


    public static $definition = [
        'table' => 's2i_lists',
        'primary' => 'id_s2i_list',
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'slider' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false],
            'speed' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => false],
            'image' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => false, 'size' => 255],

        ],
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
    }
    public static function getAllLists()
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 's2i_lists`';
        return Db::getInstance()->executeS($sql);
    }
}
