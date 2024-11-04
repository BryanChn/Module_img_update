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
                $language['is_default'] = 0; // Définir is_default à 0 si non défini
            }
        }

        // Récupération des données multilingues
        $langData = [];
        foreach ($languages as $lang) {
            $langData[$lang['id_lang']] = Db::getInstance()->getRow(
                '
                SELECT * FROM ' . _DB_PREFIX_ . 's2i_section_details_lang 
                WHERE id_s2i_detail = ' . (int)$details['id_s2i_detail'] . ' 
                AND id_lang = ' . (int)$lang['id_lang']
            );
        }

        // Configuration du helper
        $helper = new HelperForm();
        $helper->module = $module;
        $helper->name_controller = 'AdminS2iImage';
        $helper->identifier = 'id_s2i_section';
        $helper->currentIndex = Context::getContext()->link->getAdminLink('AdminS2iImage') . '&action=edit&id_s2i_section=' . $id_s2i_section;
        $helper->token = Tools::getAdminTokenLite('AdminS2iImage');
        $helper->submit_action = 'submit_update_section';
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;

        // Valeurs des champs
        $helper->fields_value = [
            'id_s2i_section' => $id_s2i_section,
            'name' => $section->name,
            'active' => $section->active,
            'slider' => $section->slider,
            'speed' => $section->speed,
            'only_title' => $details['only_title'],
            'image_mobile_enabled' => $details['image_is_mobile']
        ];

        // Ajout des valeurs multilingues
        foreach ($languages as $lang) {
            $id_lang = (int)$lang['id_lang'];
            if (isset($langData[$id_lang])) {
                $helper->fields_value['title_' . $id_lang] = $langData[$id_lang]['title'];
                $helper->fields_value['legend_' . $id_lang] = $langData[$id_lang]['legend'];
                $helper->fields_value['url_' . $id_lang] = $langData[$id_lang]['url'];
            }
        }

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
                    [
                        'type' => 'hidden',
                        'name' => 'id_s2i_section'
                    ],
                    [
                        'type' => 'text',
                        'label' => $module->l('Nom'),
                        'name' => 'name',
                        'required' => true,
                        'class' => 'fixed-width-xl'
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
                        'name' => 'slider',
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
                        'class' => 'fixed-width-sm',
                        'suffix' => 'ms'
                    ],
                    [
                        'type' => 'switch',
                        'label' => $module->l('Titre uniquement'),
                        'name' => 'only_title',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'only_title_on', 'value' => 1, 'label' => $module->l('Oui')],
                            ['id' => 'only_title_off', 'value' => 0, 'label' => $module->l('Non')]
                        ]
                    ]
                ],
                'submit' => [
                    'title' => $module->l('Enregistrer'),
                    'class' => 'btn btn-default pull-right'
                ]
            ]
        ];
        $fields_form['form']['input'][] = [
            'type' => 'switch',
            'label' => $module->l('Image mobile'),
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
        ];
        // Ajout des champs multilingues
        foreach ($languages as $lang) {
            $id_lang = (int)$lang['id_lang'];

            // Champs texte multilingues
            $fields_form['form']['input'][] = [
                'type' => 'text',
                'label' => $module->l('Titre') . ' (' . $lang['name'] . ')',
                'name' => 'title_' . $id_lang,
                'required' => false,

            ];

            $fields_form['form']['input'][] = [
                'type' => 'text',
                'label' => $module->l('Légende') . ' (' . $lang['name'] . ')',
                'name' => 'legend_' . $id_lang,
                'required' => false,
                'class' => 'optional-field'
            ];

            $fields_form['form']['input'][] = [
                'type' => 'text',
                'label' => $module->l('URL') . ' (' . $lang['name'] . ')',
                'name' => 'url_' . $id_lang,
                'required' => false,
                'class' => 'optional-field'
            ];

            // Image existante et upload
            if (isset($langData[$id_lang]) && !empty($langData[$id_lang]['image'])) {
                $imageUrl = _PS_IMG_ . $langData[$id_lang]['image'];
                $fields_form['form']['input'][] = [
                    'type' => 'html',
                    'name' => 'current_image_' . $id_lang,
                    'html_content' => '
                        <div class="form-group optional-field">
                            <label class="control-label col-lg-3">' . $module->l('Image actuelle') . ' (' . $lang['name'] . ')</label>
                            <div class="col-lg-9">
                                <img src="' . $imageUrl . '" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        </div>'
                ];
            }

            $fields_form['form']['input'][] = [
                'type' => 'file',
                'label' => $module->l('Nouvelle image') . ' (' . $lang['name'] . ')',
                'name' => 'image_' . $id_lang,
                'required' => false,
                'desc' => $module->l('Télécharger une nouvelle image pour cette section.'),
                'class' => 'optional-field'
            ];
        }

        return $helper->generateForm([$fields_form]);
    }
}
