<?php

class HelperEditSection
{
    public static function renderEditForm($module, $id_s2i_section)
    {


        // Récupération des données
        $section = new Sections($id_s2i_section);
        $details = SectionDetails::getBySectionId($id_s2i_section);
        $languages = Language::getLanguages(false);
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $lang_id = Context::getContext()->language->id;
        foreach ($languages as &$language) {
            if (!isset($language['is_default'])) {
                $language['is_default'] = 0; // Définir `is_default` à 0 si non défini
            }
        }
        // Configuration du helper
        $helper = new HelperForm();
        $helper->default_form_language = $lang_id;
        $helper->module = $module;
        $helper->name_controller = 'AdminS2iImage';
        $helper->identifier = 'id_s2i_section';
        $helper->currentIndex = Context::getContext()->link->getAdminLink('AdminS2iImage') . '&action=edit&id_s2i_section=';
        $helper->submit_action = 'submit_update_section';
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;


        // Configuration multilingue
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->languages = $languages;

        // Structure du formulaire
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $module->l('Modifier la Section'),
                    'icon' => 'icon-cogs'
                ],
                'input' => [
                    // ID (caché)
                    [
                        'type' => 'hidden',
                        'name' => 'id_s2i_section'
                    ],
                    // Nom
                    [
                        'type' => 'text',
                        'label' => $module->l('Nom'),
                        'name' => 'name',
                        'required' => true,
                        'class' => 'fixed-width-xl'
                    ],
                    // Actif
                    [
                        'type' => 'switch',
                        'label' => $module->l('Actif'),
                        'name' => 'active',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $module->l('Oui')
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $module->l('Non')
                            ]
                        ]
                    ],
                    // Slider
                    [
                        'type' => 'switch',
                        'label' => $module->l('Slider'),
                        'name' => 'slider',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'slider_on',
                                'value' => 1,
                                'label' => $module->l('Oui')
                            ],
                            [
                                'id' => 'slider_off',
                                'value' => 0,
                                'label' => $module->l('Non')
                            ]
                        ],
                        'desc' => $module->l('Activer le mode slider')
                    ],
                    // Vitesse
                    [
                        'type' => 'text',
                        'label' => $module->l('Vitesse'),
                        'name' => 'speed',
                        'class' => 'fixed-width-sm',
                        'suffix' => 'ms',
                        'desc' => $module->l('Vitesse en millisecondes')
                    ],
                    // Titre uniquement
                    [
                        'type' => 'switch',
                        'label' => $module->l('Titre uniquement'),
                        'name' => 'only_title',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'only_title_on',
                                'value' => 1,
                                'label' => $module->l('Oui')
                            ],
                            [
                                'id' => 'only_title_off',
                                'value' => 0,
                                'label' => $module->l('Non')
                            ]
                        ]
                    ],
                    // Image mobile
                    [
                        'type' => 'switch',
                        'label' => $module->l('Image mobile'),
                        'name' => 'image_mobile_enabled',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'image_mobile_on',
                                'value' => 1,
                                'label' => $module->l('Oui')
                            ],
                            [
                                'id' => 'image_mobile_off',
                                'value' => 0,
                                'label' => $module->l('Non')
                            ]
                        ]
                    ],
                    // Titre (multilingue)
                    [
                        'type' => 'text',
                        'label' => $module->l('Titre'),
                        'name' => 'title',
                        'lang' => true,
                        'required' => false
                    ],
                    // Légende (multilingue)
                    [
                        'type' => 'text',
                        'label' => $module->l('Légende'),
                        'name' => 'legend',
                        'lang' => true,
                        'required' => false
                    ],
                    // URL (multilingue)
                    [
                        'type' => 'text',
                        'label' => $module->l('URL'),
                        'name' => 'url',
                        'lang' => true,
                        'required' => false
                    ],
                    // Image (multilingue)
                    [
                        'type' => 'file',
                        'label' => $module->l('Image'),
                        'name' => 'image',
                        'lang' => true,
                        'required' => false,
                        'desc' => $module->l('Format recommandé : JPG, PNG ')
                    ]
                ],
                'submit' => [
                    'title' => $module->l('Enregistrer'),
                    'class' => 'btn btn-default pull-right'
                ]
            ]
        ];

        // Valeurs des champs
        $helper->fields_value = [
            'id_s2i_section' => $id_s2i_section,
            'name' => $section->name,
            'active' => $section->active,
            'slider' => $section->slider,
            'speed' => $section->speed,
            'only_title' => isset($details->only_title) ? $details->only_title : 0,
            'image_mobile_enabled' => isset($details->image_is_mobile) ? $details->image_is_mobile : 0
        ];

        // Ajout des valeurs multilingues
        foreach ($languages as $lang) {
            $id_lang = $lang['id_lang'];
            $helper->fields_value['title'][$id_lang] = isset($details->title[$id_lang]) ? $details->title[$id_lang] : '';
            $helper->fields_value['legend'][$id_lang] = isset($details->legend[$id_lang]) ? $details->legend[$id_lang] : '';
            $helper->fields_value['url'][$id_lang] = isset($details->url[$id_lang]) ? $details->url[$id_lang] : '';
            $helper->fields_value['image'][$id_lang] = isset($details->image[$id_lang]) ? $details->image[$id_lang] : '';
        }


        return $helper->generateForm([$fields_form]);
    }
}
