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

        $hooks = [
            ['id' => 'displayHome', 'name' => $this->module->l('Accueil')],
            ['id' => 'displayFooter', 'name' => $this->module->l('Pied de page')],
            ['id' => 'displayProduct', 'name' => $this->module->l('Produit')],
            // ['id' => 'displayJolisearch', 'name' => $this->module->l('Menu de recherche jolisearch hello-moon')],
            ['id' => 'displaySearch', 'name' => $this->module->l('Menu de recherche')],
            ['id' => 'displaySlideTitle', 'name' => $this->module->l('test')],
        ];

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
                        'type' => 'select',
                        'label' => $this->module->l('Disposition'),
                        'name' => 'hook_location',
                        'options' => [
                            'query' => $hooks,
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],


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
        $helper->base_folder = _PS_MODULE_DIR_ . 's2i_update_img/views/templates/admin/extendFormSection/helpers/form/';
        return $helper->generateForm([$fields_form]);
    }
}
