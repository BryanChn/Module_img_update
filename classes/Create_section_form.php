<?php





class Create_section_form
{
    private $module;
    private $context;

    public function __construct($module)
    {
        $this->module = $module;
        $this->context = Context::getContext();
    }


    public function renderForm()
    {
        $languages = Language::getLanguages();
        $lang_id = $this->context->language->id;
        foreach ($languages as &$language) {
            if (!isset($language['is_default'])) {
                $language['is_default'] = 0; // Définir `is_default` à 0 si non défini
            }
        }

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('Créer une nouvelle section'),
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->module->l('Nom'),
                        'name' => 'name',
                        'required' => true,
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->module->l('Actif'),
                        'name' => 'active',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->module->l('Oui')
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->module->l('Non')
                            ]
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->module->l('Slider'),
                        'name' => 'slider',
                        'values' => [
                            [
                                'id' => 'slider_on',
                                'value' => 1,
                                'label' => $this->module->l('Oui')
                            ],
                            [
                                'id' => 'slider_off',
                                'value' => 0,
                                'label' => $this->module->l('Non')
                            ]
                        ],
                        'desc' => $this->module->l('Si activé, vous pouvez choissir sa vitesse.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->module->l('Vitesse'),
                        'name' => 'speed',
                        'desc' => $this->module->l('Vitesse en millisecondes'),
                        'required' => false,
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->module->l('Titre seulement ?'),
                        'name' => 'only_title',
                        'values' => [
                            [
                                'id' => 'only_title_on',
                                'value' => 1,
                                'label' => $this->module->l('Oui')
                            ],
                            [
                                'id' => 'only_title_off',
                                'value' => 0,
                                'label' => $this->module->l('Non')
                            ]
                        ],
                        'desc' => $this->module->l('Si activé, seul le titre sera affiché.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->module->l('Titre'),
                        'name' => 'title',
                        'lang' => true,
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->module->l('Légende'),
                        'name' => 'legend',
                        'lang' => true,
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->module->l('URL'),
                        'name' => 'url',
                        'lang' => true,
                        'required' => true,
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->module->l('Image pour mobile ?'),
                        'name' => 'image_mobile_enabled',
                        'values' => [
                            [
                                'id' => 'image_mobile_enabled_on',
                                'value' => 1,
                                'label' => $this->module->l('Oui')
                            ],
                            [
                                'id' => 'image_mobile_enabled_off',
                                'value' => 0,
                                'label' => $this->module->l('Non')
                            ]
                        ],
                        'desc' => $this->module->l('Si activé, l\'image sera affichée sur mobile.'),
                    ],

                ],

                'submit' => [
                    'title' => $this->module->l('Enregistrer'),
                    'class' => 'btn btn-default pull-right',


                ]
            ],
        ];
        // gestion des images pour chaques langues 
        foreach ($languages as $lang) {
            $fields_form['form']['input'][] = [
                'type' => 'file',
                'label' => $this->module->l('Image') . ' (' . $lang['name'] . ')',
                'name' => 'image_' . $lang['id_lang'],
                'lang' => true,
                'required' => false,
                'desc' => $this->module->l('Télécharger une image pour cette section.'),
            ];
        }


        $helper = new HelperForm();
        $helper->fields_value = [
            'name' => Tools::getValue('name', ''),
            'active' => Tools::getValue('active', 0),
            'slider' => Tools::getValue('slider', 0),
            'speed' => Tools::getValue('speed', 5000),
            'only_title' => Tools::getValue('only_title', 0),
            'image_mobile_enabled' => Tools::getValue('image_mobile_enabled', 0),
            'is_default' => Tools::getValue('is_default', 0),

        ];
        $helper->module = $this->module;
        $helper->name_controller = 'create_section_form';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->languages = $languages;
        $helper->default_form_language = $lang_id;
        $helper->title = $this->module->l('Create New Section');
        $helper->submit_action = 'submit_create_section';
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->module->name;


        return $helper->generateForm([$fields_form]);
    }
}
