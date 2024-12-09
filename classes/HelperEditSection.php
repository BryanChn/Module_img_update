<?php

class HelperEditSection
{
    public static function renderEditForm($module, $id_section)
    {
        $section = new Section($id_section);
        $hook_locations = HookLocation::getHookLocations($id_section);
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->module = $module;
        $helper->name_controller = 'AdminS2iGestionlist';
        $helper->identifier = 'id_section';
        $helper->submit_action = 'submit_update_section_only';
        $helper->currentIndex = Context::getContext()->link->getAdminLink('AdminS2iGestionlist');
        $helper->token = Tools::getAdminTokenLite('AdminS2iGestionlist');


        $helper->fields_value = [
            'id_section' => $id_section,
            'name' => $section->name,
            'active' => $section->active,
            'is_slider' => $section->is_slider,
            'speed' => $section->speed,
            'position' => $section->position,
            'hook_location[]' => $hook_locations
        ];
        // différent hooks possibles && ajoutez ici les hooks que vous souhaitez
        $hooks = [
            ['id' => 'displayFooter', 'name' => $module->l('Pied de page')],
            ['id' => 'displaySlideTitle', 'name' => $module->l('Titre des slides')],
        ];

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $module->l('Paramètres de la section'),
                    'icon' => 'icon-cogs'
                ],
                'input' => [
                    [
                        'type' => 'hidden',
                        'name' => 'id_section'
                    ],
                    [
                        'type' => 'text',
                        'label' => $module->l('Nom'),
                        'name' => 'name',
                        'required' => true
                    ],
                    [
                        'type' => 'switch',
                        'label' => $module->l('Actif'),
                        'name' => 'active',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'active_on', 'value' => 1, 'label' => $module->l('Oui')],
                            ['id' => 'active_off', 'value' => 0, 'label' => $module->l('Non')]
                        ]
                    ],
                    [
                        'type' => 'switch',
                        'label' => $module->l('Slider'),
                        'name' => 'is_slider',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'slider_on', 'value' => 1, 'label' => $module->l('Oui')],
                            ['id' => 'slider_off', 'value' => 0, 'label' => $module->l('Non')]
                        ],
                        'desc' => $module->l('Si activé, vous pouvez choisir sa vitesse.') .
                            '<script>
                    $(document).ready(function() {
                        function toggleSpeedField() {
                            if ($("input[name=\'is_slider\']:checked").val() == 1) {
                                $("input[name=\'speed\']").closest(".form-group").show();
                            } else {
                                $("input[name=\'speed\']").closest(".form-group").hide();
                            }
                        }
                        
                        toggleSpeedField();
                        $("input[name=\'is_slider\']").change(toggleSpeedField);
                        });
                    </script>'
                    ],
                    [
                        'type' => 'text',
                        'label' => $module->l('Vitesse'),
                        'name' => 'speed',
                        'suffix' => 'ms',


                    ],
                    [
                        'type' => 'select',
                        'label' => $module->l('Disposition'),
                        'name' => 'hook_location[]',
                        'options' => [
                            'query' => $hooks,
                            'id' => 'id',
                            'name' => 'name',
                        ],
                        'multiple' => true,
                        'desc' => $module->l('Sélectionnez un ou plusieurs emplacements'),
                        'class' => 'chosen'
                    ],
                ],
                'submit' => [
                    'title' => $module->l('Enregistrer'),
                    'class' => 'btn btn-default pull-right'
                ]
            ]
        ];

        return $helper->generateForm([$fields_form]);
    }

    public static function renderEditSlideForm($module, $id_slide)
    {
        $slide = new Slide($id_slide);
        $id_section = $slide->id_section;
        $languages = Language::getLanguages(false);
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');




        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->module = $module;
        $helper->name_controller = 'AdminS2iGestionlist';
        $helper->identifier = 'id_slide';
        $helper->submit_action = 'submit_update_slide';
        $helper->currentIndex = Context::getContext()->link->getAdminLink('AdminS2iGestionlist', true);
        $helper->token = Tools::getAdminTokenLite('AdminS2iGestionlist');
        $helper->base_folder = _PS_MODULE_DIR_ . 's2i_gestionlist/views/templates/admin/extendFormSlide/helpers/form/';



        $slideLangsData = [];
        foreach ($languages as $lang) {
            $langData = SlideLang::getBySlideAndLang($id_slide, $lang['id_lang']);
            if ($langData) {
                $slideLangsData[$lang['id_lang']] = $langData;
            }
        }

        // Initialisation des valeurs
        $helper->fields_value = [
            'id_slide' => $id_slide,
            'id_section' => $id_section,
            'active' => $slide->active,
            'only_title' => $slide->only_title,
            'title_hide' => $slide->title_hide,
            'image_is_mobile' => $slide->image_is_mobile
        ];

        // Ajout des valeurs multilingues
        foreach ($languages as $lang) {
            $id_lang = $lang['id_lang'];
            if (isset($slideLangsData[$id_lang])) {
                $currentLang = $slideLangsData[$id_lang];
                $helper->fields_value['title_' . $id_lang] = $currentLang['title'];
                $helper->fields_value['legend_' . $id_lang] = $currentLang['legend'];
                $helper->fields_value['url_' . $id_lang] = $currentLang['url'];


                $sql = 'SELECT sl.image 
                        FROM ' . _DB_PREFIX_ . 's2i_slides_lang sl
                        WHERE sl.id_slide = ' . (int)$id_slide . ' 
                        AND sl.id_lang = ' . $id_lang;

                $image = Db::getInstance()->getValue($sql);
                if ($image) {
                    $helper->fields_value['image' . $id_lang] = _PS_IMG_ . $image;
                }
                if (!empty($currentLang['image_mobile'])) {
                    $helper->fields_value['image_mobile' . $id_lang] = _PS_IMG_ . $currentLang['image_mobile'];
                }
            }
        }


        // Configuration des langues
        $helper->languages = [];
        foreach ($languages as $lang) {
            $helper->languages[] = array(
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
            );
        }
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : $default_lang;




        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $module->l('Édition du slide'),
                    'icon' => 'icon-edit'
                ],
                'input' => [
                    [
                        'type' => 'hidden',
                        'name' => 'id_section',
                        'value' => $id_section
                    ],
                    [
                        'type' => 'hidden',
                        'name' => 'id_slide',
                        'value' => $id_slide
                    ],
                    [
                        'type' => 'switch',
                        'label' => $module->l('Actif'),
                        'name' => 'active',
                        'values' => [
                            ['id' => 'active_on', 'value' => 1, 'label' => $module->l('Oui')],
                            ['id' => 'active_off', 'value' => 0, 'label' => $module->l('Non')]
                        ],
                        'group_name' => 'active'
                    ],
                    [
                        'type' => 'switch',
                        'label' => $module->l('Titre seulement'),
                        'name' => 'only_title',
                        'values' => [
                            ['id' => 'only_title_on', 'value' => 1, 'label' => $module->l('Oui')],
                            ['id' => 'only_title_off', 'value' => 0, 'label' => $module->l('Non')]
                        ],
                        'group_name' => 'only_title'
                    ],
                    [
                        'type' => 'text',
                        'label' => $module->l('Titre'),
                        'name' => 'title',
                        'lang' => true,
                        'required' => true,
                        'group_name' => 'text_fields'
                    ],
                    [
                        'type' => 'text',
                        'label' => $module->l('Légende'),
                        'name' => 'legend',
                        'lang' => true,
                        'class' => 'legend-url-group',
                        'form_group_class' => 'legend-url-group',
                        'group_name' => 'text_fields'
                    ],
                    [
                        'type' => 'text',
                        'label' => $module->l('URL'),
                        'name' => 'url',
                        'lang' => true,
                        'class' => 'legend-url-group',
                        'form_group_class' => 'legend-url-group',
                        'group_name' => 'text_fields'
                    ],
                    [
                        'type' => 'switch',
                        'label' => $module->l('Image mobile ?'),
                        'name' => 'image_is_mobile',
                        'values' => [
                            ['id' => 'image_is_mobile_on', 'value' => 1, 'label' => $module->l('Oui')],
                            ['id' => 'image_is_mobile_off', 'value' => 0, 'label' => $module->l('Non')]
                        ],
                        'group_name' => 'image_is_mobile'
                    ],
                    [
                        'type' => 'file_lang',
                        'label' => $module->l('Voulez-vous remplacer l\'image actuelle ?'),
                        'name' => 'image',
                        'lang' => true,
                        'required' => false,
                        'display_image' => true,
                        'group_name' => 'image',
                        'form_group_class' => 'image',

                    ],

                    [
                        'type' => 'file_lang',
                        'label' => $module->l('Voulez-vous remplacer l\'image mobile actuelle ?'),
                        'name' => 'image_mobile',
                        'lang' => true,
                        'required' => false,
                        'form_group_class' => 'mobile-image',
                        'display_image' => true,
                        'group_name' => 'image_mobile'
                    ],
                ],
                'submit' => [
                    'title' => $module->l('Enregistrer'),
                    'class' => 'btn btn-default pull-right'
                ]
            ]
        ];



        return $helper->generateForm([$fields_form]);
    }
}
