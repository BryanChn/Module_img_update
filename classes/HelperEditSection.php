<?php
class HelperEditSection
{

    public static function getAllinfoSection()
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 's2i_sections';
        return Db::getInstance()->executeS($sql);
    }
    public static function renderEditForm()
    {
        // Récupération de l'ID de la section depuis la requête
        $id_s2i_section = (int) Tools::getValue('id_s2i_section');

        // Récupérer les informations de la section et des tables liées
        $section = new Sections($id_s2i_section);
        $details = SectionDetails::getBySectionId($id_s2i_section);
        $languages = Language::getLanguages();

        // Création de l'instance HelperForm
        $helper = new HelperForm();
        $helper->submit_action = 'submitUpdateSection';
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . Tools::getValue('configure') . '&id_s2i_section=' . $id_s2i_section;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->title = 'Edit Section';

        // Définition des champs pour le formulaire
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => 'Modifier la section',
                    'icon' => 'icon-edit'
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => 'Nom',
                        'name' => 'name',
                        'required' => true,
                        'value' => $section->name,
                    ],
                    [
                        'type' => 'switch',
                        'label' => 'Actif',
                        'name' => 'active',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => 'Enabled'
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => 'Disabled'
                            ]
                        ],
                        'value' => $section->active,
                    ],
                    [
                        'type' => 'switch',
                        'label' => 'Slider',
                        'name' => 'slider',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'slider_on',
                                'value' => 1,
                                'label' => 'Oui'
                            ],
                            [
                                'id' => 'slider_off',
                                'value' => 0,
                                'label' => 'Non'
                            ]
                        ],
                        'value' => $section->slider,
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Vitesse',
                        'name' => 'speed',
                        'value' => $section->speed,
                    ],
                ],
                'submit' => [
                    'title' => 'Enregistrer les modifications',
                    'class' => 'btn btn-default pull-right'
                ]
            ]
        ];

        // Ajouter les champs multilingues pour les détails
        foreach ($languages as $lang) {
            $fields_form['form']['input'][] = [
                'type' => 'text',
                'label' => 'Titre (' . $lang['name'] . ')',
                'name' => 'title_' . $lang['id_lang'],
                'lang' => true,
                'value' => $details['title_' . $lang['id_lang']] ?? '',
            ];
            $fields_form['form']['input'][] = [
                'type' => 'textarea',
                'label' => 'Légende (' . $lang['name'] . ')',
                'name' => 'legend_' . $lang['id_lang'],
                'lang' => true,
                'value' => $details['legend_' . $lang['id_lang']] ?? '',
            ];
            $fields_form['form']['input'][] = [
                'type' => 'file',
                'label' => 'Image (' . $lang['name'] . ')',
                'name' => 'image_' . $lang['id_lang'],
                'lang' => true,
            ];
        }

        // Définir les valeurs actuelles des champs pour le formulaire
        $helper->fields_value = self::getFormValues($section, $details, $languages);

        // Générer le formulaire

        return $helper->generateForm([$fields_form]);
    }

    private static function getFormValues($section, $details, $languages)
    {
        $fields_value = [
            'name' => $section->name,
            'active' => $section->active,
            'slider' => $section->slider,
            'speed' => $section->speed,
        ];

        foreach ($languages as $lang) {
            $fields_value['title_' . $lang['id_lang']] = $details['title_' . $lang['id_lang']] ?? '';
            $fields_value['legend_' . $lang['id_lang']] = $details['legend_' . $lang['id_lang']] ?? '';
        }
        return $fields_value;
    }
}
