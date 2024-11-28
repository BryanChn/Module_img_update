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


        $helper = new HelperForm();
        $helper->module = $this->module;
        $helper->name_controller = 'AdminS2iImage';
        $helper->currentIndex = Context::getContext()->link->getAdminLink('AdminS2iImage');
        $helper->token = Tools::getAdminTokenLite('AdminS2iImage');
        $helper->submit_action = 'submit_create_section';
        $helper->show_toolbar = false;
        $helper->show_cancel_button = true;
        $helper->fields_value = [
            'name' => Tools::getValue('name', ''),
            'active' => Tools::getValue('active', 1),
            'is_slider' => Tools::getValue('is_slider', 0),
            'speed' => Tools::getValue('speed', 5000),
            'hook_location[]' => Tools::getValue('hook_location', [])
        ];

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
                        'name' => 'hook_location[]',
                        'multiple' => true,
                        'options' => [
                            'query' => $hooks,
                            'id' => 'id',
                            'name' => 'name',
                        ],
                        'desc' => $this->module->l('Sélectionnez un ou plusieurs emplacements'),
                        'class' => 'chosen'
                    ],


                ],
                'submit' => [
                    'title' => $this->module->l('Enregistrer'),
                    'class' => 'btn btn-default pull-right',

                ]
            ],
        ];
        return $helper->generateForm([$fields_form]);
    }
}
