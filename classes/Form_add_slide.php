<?php

class Form_add_slide
{
    public function renderFormAddSlide($module, $id_section)
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->module = $module;
        $helper->name_controller = 'AdminS2iGestionlist';
        $helper->identifier = 'id_slide';
        $helper->submit_action = 'submit_add_slide';
        $helper->currentIndex = Context::getContext()->link->getAdminLink('AdminS2iGestionlist');
        $helper->token = Tools::getAdminTokenLite('AdminS2iGestionlist');
        $helper->base_folder = _PS_MODULE_DIR_ . 's2i_gestionlist/views/templates/admin/extendFormAddSlide/helpers/form/';
        $languages = Language::getLanguages(false);
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : $default_lang;

        $helper->fields_value = [
            'id_section' => $id_section,
            'active' => 1,
            'only_title' => 0,
            'image_is_mobile' => 0,
            'display_datePicker' => 0,
            'start_date' => date('Y-m-d H:i:s'),
            'end_date' => date('Y-m-d H:i:s', strtotime('+1 year')),
        ];


        $helper->languages = [];
        foreach ($languages as $lang) {
            $helper->languages[] = array(
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
            );

            $id_lang = (int)$lang['id_lang'];
            $helper->fields_value['title_' . $id_lang] = Tools::getValue('title_' . $id_lang, '');
            $helper->fields_value['legend_' . $id_lang] = Tools::getValue('legend_' . $id_lang, '');
            $helper->fields_value['url_' . $id_lang] = Tools::getValue('url_' . $id_lang, '');
            $helper->fields_value['image_' . $id_lang] = '';
        }

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $module->l('Ajouter une slide'),
                ],
                'input' => [
                    [
                        'type' => 'hidden',
                        'name' => 'id_section',
                        'value' => $id_section
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
                        'label' => $module->l('Ajouter une image'),
                        'name' => 'image',
                        'lang' => true,
                        'required' => false,
                        'display_image' => true,
                        'group_name' => 'image',
                        'form_group_class' => 'image',

                    ],

                    [
                        'type' => 'file_lang',
                        'label' => $module->l('Ajouter une image mobile'),
                        'name' => 'image_mobile',
                        'lang' => true,
                        'required' => false,
                        'form_group_class' => 'mobile-image',
                        'display_image' => true,
                        'group_name' => 'image_mobile'
                    ],
                    [
                        'type' => 'switch',
                        'label' => $module->l('Activer les dates'),
                        'name' => 'display_datePicker',
                        'values' => [
                            ['id' => 'display_datePicker_on', 'value' => 1, 'label' => $module->l('Oui')],
                            ['id' => 'display_datePicker_off', 'value' => 0, 'label' => $module->l('Non')]
                        ],
                        'group_name' => 'display_datePicker'
                    ],

                    [
                        'type' => 'datetime',
                        'label' => $module->l('Date de début'),
                        'name' => 'start_date',
                        'required' => true
                    ],
                    [
                        'type' => 'datetime',
                        'label' => $module->l('Date de fin'),
                        'name' => 'end_date',
                        'required' => true
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
