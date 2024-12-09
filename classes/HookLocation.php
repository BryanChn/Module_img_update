<?php
class HookLocation extends ObjectModel
{
    public $id_hook_location;
    public $id_section;
    public $hook_name;

    public static $definition = [
        'table' => 's2i_section_hooks',
        'primary' => 'id_hook_location',
        'fields' => [
            'id_section' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'hook_name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
        ],
    ];

    public static function getHookLocations($id_section)
    {
        $results = Db::getInstance()->executeS(
            '
            SELECT hook_name 
            FROM `' . _DB_PREFIX_ . 's2i_section_hooks`
            WHERE `id_section` = ' . (int)$id_section
        );

        // Retourne un tableau simple des noms de hooks
        return array_column($results, 'hook_name');
    }

    public static function getSectionsByHook($hook_name)
    {
        return Db::getInstance()->executeS('
            SELECT s.* 
            FROM `' . _DB_PREFIX_ . 's2i_sections` s
            JOIN `' . _DB_PREFIX_ . 's2i_section_hooks` sh ON s.id_section = sh.id_section
            WHERE sh.hook_name = "' . pSQL($hook_name) . '"
            AND s.active = 1
            ORDER BY s.position ASC
        ');
    }
}
