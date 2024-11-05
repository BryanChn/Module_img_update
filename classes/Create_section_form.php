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
                        'name' => 'is_slider',
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
                        'desc' => $this->module->l('Si activé, vous pouvez choisir sa vitesse.'),
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
                        'type' => 'switch',
                        'label' => $this->module->l('Cacher le titre ?'),
                        'name' => 'title_hide',
                        'values' => [
                            [
                                'id' => 'title_hide_on',
                                'value' => 1,
                                'label' => $this->module->l('Oui')
                            ],
                            [
                                'id' => 'title_hide_off',
                                'value' => 0,
                                'label' => $this->module->l('Non')
                            ]
                        ],
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
                        'class' => 'optional-field',
                        'form_group_class' => 'legend-url-group',
                        'group_name' => 'text_fields'
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->module->l('URL'),
                        'name' => 'url',
                        'lang' => true,
                        'class' => 'optional-field',
                        'form_group_class' => 'legend-url-group',
                        'group_name' => 'text_fields'
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->module->l('Image pour mobile ?'),
                        'name' => 'image_is_mobile',
                        'class' => 'optional-field',
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

                    [
                        'type' => 'file_lang',
                        'label' => $this->module->l('Image non mobile'),
                        'name' => 'image',
                        'required' => false,
                        'group_name' => 'images',
                        'class' => 'optional-field',

                    ],

                    [
                        'type' => 'file_lang',
                        'name' => 'image_mobile',
                        'required' => false,
                        'group_name' => 'images_mobile',
                        'class' => 'optional-field',
                        'label' => $this->module->l('Image mobile'),
                        'form_group_class' => 'mobile-image',
                    ]
                ],
                'submit' => [
                    'title' => $this->module->l('Enregistrer'),
                    'class' => 'btn btn-default pull-right',
                ]
            ],
        ];


        $helper = new HelperForm();
        $helper->fields_value = [
            'name' => Tools::getValue('name', ''),
            'active' => Tools::getValue('active', 1),
            'is_slider' => Tools::getValue('is_slider', 0),
            'speed' => Tools::getValue('speed', 5000),
            'position' => Tools::getValue('position', 0),
            'hook_location' => Tools::getValue('hook_location', ''),
            'only_title' => Tools::getValue('only_title', 0),
            'title_hide' => Tools::getValue('title_hide', 0),
            'image_is_mobile' => Tools::getValue('image_is_mobile', 0),

        ];
        $languages = Language::getLanguages(false);
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Préparation du tableau des langues avec is_default
        $helper->languages = [];
        foreach ($languages as $lang) {
            $helper->languages[] = array(
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
            );

            // Initialisation des valeurs pour chaque langue
            $id_lang = (int)$lang['id_lang'];
            $helper->fields_value['title_' . $id_lang] = Tools::getValue('title_' . $id_lang, '');
            $helper->fields_value['legend_' . $id_lang] = Tools::getValue('legend_' . $id_lang, '');
            $helper->fields_value['url_' . $id_lang] = Tools::getValue('url_' . $id_lang, '');
            $helper->fields_value['image_' . $id_lang] = '';
        }

        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : $default_lang;

        $helper->module = $this->module;
        $helper->name_controller = 'create_section_form';
        $helper->token = Tools::getAdminTokenLite('AdminS2iImage');


        $helper->title = $this->module->l('Create New Section');
        $helper->submit_action = 'submit_create_section';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminS2iImage', true);
        $helper->tpl_vars = array(
            'template' => 'form.tpl'
        );
        $helper->base_folder = _PS_MODULE_DIR_ . 's2i_update_img/views/templates/admin/';
        return $helper->generateForm([$fields_form]);
    }
}
