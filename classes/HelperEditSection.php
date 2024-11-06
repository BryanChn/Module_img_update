<?php

class HelperEditSection
{
    public static function renderEditForm($module, $id_section)
    {
        $section = new Section($id_section);

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->module = $module;
        $helper->name_controller = 'AdminS2iImage';
        $helper->identifier = 'id_section';
        $helper->submit_action = 'submit_update_section';
        $helper->currentIndex = Context::getContext()->link->getAdminLink('AdminS2iImage');
        $helper->token = Tools::getAdminTokenLite('AdminS2iImage');
        $helper->base_folder = _PS_MODULE_DIR_ . 's2i_update_img/views/templates/admin/extendFormSection/';
        $helper->tpl_vars = array(
            'template' => 'form.tpl'
        );

        $helper->fields_value = [
            'id_section' => $id_section,
            'name' => $section->name,
            'active' => $section->active,
            'is_slider' => $section->is_slider,
            'speed' => $section->speed,
            'position' => $section->position,
            'hook_location' => $section->hook_location
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
                        ]
                    ],
                    [
                        'type' => 'text',
                        'label' => $module->l('Vitesse'),
                        'name' => 'speed',
                        'suffix' => 'ms'
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
        $languages = Language::getLanguages(false);
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->module = $module;
        $helper->name_controller = 'AdminS2iImage';
        $helper->identifier = 'id_slide';
        $helper->submit_action = 'submit_update_slide';
        $helper->currentIndex = Context::getContext()->link->getAdminLink('AdminS2iImage');
        $helper->token = Tools::getAdminTokenLite('AdminS2iImage');

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

        $helper->base_folder = _PS_MODULE_DIR_ . 's2i_update_img/views/templates/admin/extendFormSection/';
        $helper->tpl_vars = array(
            'template' => 'form.tpl'
        );

        // Initialisation des valeurs
        $helper->fields_value = [
            'id_slide' => $id_slide,
            'active' => $slide->active,
            'only_title' => $slide->only_title,
            'title_hide' => $slide->title_hide,
            'image_is_mobile' => $slide->image_is_mobile
        ];

        // Ajout des valeurs multilingues
        foreach ($languages as $lang) {
            $slideLang = SlideLang::getBySlideAndLang($id_slide, $lang['id_lang']);
            if ($slideLang) {
                $helper->fields_value['title_' . $lang['id_lang']] = $slideLang['title'];
                $helper->fields_value['legend_' . $lang['id_lang']] = $slideLang['legend'];
                $helper->fields_value['url_' . $lang['id_lang']] = $slideLang['url'];
                $helper->fields_value['image_' . $lang['id_lang']] = $slideLang['image'];
                $helper->fields_value['image_mobile_' . $lang['id_lang']] = $slideLang['image_mobile'];
            }
        }

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $module->l('Édition du slide'),
                    'icon' => 'icon-edit'
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $module->l('Actif'),
                        'name' => 'active',
                        'values' => [
                            ['id' => 'active_on', 'value' => 1, 'label' => $module->l('Oui')],
                            ['id' => 'active_off', 'value' => 0, 'label' => $module->l('Non')]
                        ]
                    ],
                    [
                        'type' => 'switch',
                        'label' => $module->l('Titre seulement'),
                        'name' => 'only_title',
                        'values' => [
                            ['id' => 'only_title_on', 'value' => 1, 'label' => $module->l('Oui')],
                            ['id' => 'only_title_off', 'value' => 0, 'label' => $module->l('Non')]
                        ]
                    ],
                    [
                        'type' => 'text',
                        'label' => $module->l('Titre'),
                        'name' => 'title',
                        'lang' => true,
                        'required' => true
                    ],
                    [
                        'type' => 'text',
                        'label' => $module->l('Légende'),
                        'name' => 'legend',
                        'lang' => true,
                        'class' => 'legend-url-group'
                    ],
                    [
                        'type' => 'text',
                        'label' => $module->l('URL'),
                        'name' => 'url',
                        'lang' => true,
                        'class' => 'legend-url-group'
                    ],
                    [
                        'type' => 'file_lang',
                        'label' => $module->l('Image'),
                        'name' => 'image',
                        'lang' => true,
                        'required' => false,
                        'group_name' => 'image'
                    ],
                    [
                        'type' => 'switch',
                        'label' => $module->l('Image mobile'),
                        'name' => 'image_is_mobile',
                        'values' => [
                            ['id' => 'image_is_mobile_on', 'value' => 1, 'label' => $module->l('Oui')],
                            ['id' => 'image_is_mobile_off', 'value' => 0, 'label' => $module->l('Non')]
                        ]
                    ],
                    [
                        'type' => 'file_lang',
                        'label' => $module->l('Image mobile'),
                        'name' => 'image_mobile',
                        'lang' => true,
                        'required' => false,
                        'mobile' => true,
                        'group_name' => 'image_mobile'
                    ]
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
