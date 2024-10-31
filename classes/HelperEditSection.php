<?php

class HelperEditSection
{
    public static function renderEditForm($module, $id_s2i_section)
    {

        // Récupération des informations de la section et des tables liées
        $section = new Sections($id_s2i_section);
        $details = SectionDetails::getBySectionId($id_s2i_section);
        $languages = Language::getLanguages();
        foreach ($languages as &$language) {
            if (!isset($language['is_default'])) {
                $language['is_default'] = 0;
            }
        }
        $helper = new HelperForm();
        $helper->module = $module;
        $helper->submit_action = 'submitUpdateSection';
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $module->name . '&id_s2i_section=' . $id_s2i_section;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->title = 'Modifier la Section';
        $helper->default_form_language = Context::getContext()->language->id;
        $helper->languages = $languages;

        // Champs du formulaire
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => 'Modifier la Section',
                    'icon' => 'icon-edit'
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $module->l('Nom'),
                        'name' => 'name',
                        'required' => true,
                    ],
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
                        ],
                    ],
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
                        'desc' => $module->l('Si activé, vous pouvez choisir sa vitesse.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $module->l('Vitesse'),
                        'name' => 'speed',
                        'desc' => $module->l('Vitesse en millisecondes'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $module->l('Titre seulement ?'),
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
                        ],
                        'desc' => $module->l('Si activé, seul le titre sera affiché.'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $module->l('Image pour mobile ?'),
                        'name' => 'image_mobile_enabled',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'image_mobile_enabled_on',
                                'value' => 1,
                                'label' => $module->l('Oui')
                            ],
                            [
                                'id' => 'image_mobile_enabled_off',
                                'value' => 0,
                                'label' => $module->l('Non')
                            ]
                        ],
                        'desc' => $module->l('Si activé, l\'image sera affichée sur mobile.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $module->l('Titre'),
                        'name' => 'title',
                        'lang' => true,
                        'required' => false,
                    ],
                    [
                        'type' => 'text',
                        'label' => $module->l('Légende'),
                        'name' => 'legend',
                        'lang' => true,
                        'required' => false,
                    ],
                    [
                        'type' => 'text',
                        'label' => $module->l('URL'),
                        'name' => 'url',
                        'lang' => true,
                        'required' => false,
                    ],
                    [
                        'type' => 'file',
                        'label' => $module->l('Image'),
                        'name' => 'image',
                        'lang' => true,
                        'required' => false,
                        'desc' => $module->l('Télécharger une image pour cette section.'),
                    ],
                ],
                'submit' => [
                    'title' => $module->l('Enregistrer les modifications'),
                    'class' => 'btn btn-default pull-right'
                ]
            ]
        ];

        // Valeurs actuelles des champs
        $helper->fields_value = self::getFormValues($section, $details, $languages);

        return $helper->generateForm([$fields_form]);
    }

    private static function getFormValues($section, $details, $languages)
    {
        $fields_value = [
            'name' => $section->name ?? '',
            'active' => $section->active ?? 0,
            'slider' => $section->slider ?? 0,
            'speed' => $section->speed ?? 5000,
            'only_title' => $section->only_title ?? 0,
            'image_mobile_enabled' => $section->image_mobile_enabled ?? 0,
            'title' => $details['title'] ?? [],
            'legend' => $details['legend'] ?? [],
            'url' => $details['url'] ?? [],
            'image' => $details['image'] ?? [],
        ];

        return $fields_value;
    }
}
