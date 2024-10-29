<?php

use Language;



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

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('Create Section'),
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
                        'label' => $this->module->l('Active'),
                        'name' => 'active',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->module->l('Yes')
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->module->l('No')
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
                                'label' => $this->module->l('Yes')
                            ],
                            [
                                'id' => 'slider_off',
                                'value' => 0,
                                'label' => $this->module->l('No')
                            ]
                        ],
                        'desc' => $this->module->l('If enabled, you can set a speed.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->module->l('Speed'),
                        'name' => 'speed',
                        'desc' => $this->module->l('Speed of the slider in ms.'),
                        'required' => false,
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->module->l('Only Title'),
                        'name' => 'only_title',
                        'values' => [
                            [
                                'id' => 'only_title_on',
                                'value' => 1,
                                'label' => $this->module->l('Yes')
                            ],
                            [
                                'id' => 'only_title_off',
                                'value' => 0,
                                'label' => $this->module->l('No')
                            ]
                        ],
                        'desc' => $this->module->l('If enabled, only the title is required.'),
                    ],
                ],
                'submit' => [
                    'title' => $this->module->l('Save'),
                    'class' => 'btn btn-default pull-right'
                ]
            ],
        ];

        // Champs multilingues pour les détails de la section
        foreach ($languages as $lang) {
            $fields_form['form']['input'][] = [
                'type' => 'text',
                'label' => $this->module->l('Title') . ' (' . $lang['name'] . ')',
                'name' => 'title_' . $lang['id_lang'],
                'lang' => true,
                'required' => false,
            ];
            $fields_form['form']['input'][] = [
                'type' => 'text',
                'label' => $this->module->l('Legend') . ' (' . $lang['name'] . ')',
                'name' => 'legend_' . $lang['id_lang'],
                'lang' => true,
                'required' => false,
            ];
            $fields_form['form']['input'][] = [
                'type' => 'text',
                'label' => $this->module->l('URL') . ' (' . $lang['name'] . ')',
                'name' => 'url_' . $lang['id_lang'],
                'lang' => true,
                'required' => false,
            ];
            $fields_form['form']['input'][] = [
                'type' => 'file',
                'label' => $this->module->l('Image') . ' (' . $lang['name'] . ')',
                'name' => 'image_' . $lang['id_lang'],
                'lang' => true,
                'required' => false,
                'desc' => $this->module->l('Upload an image for this language.')
            ];
        }

        $helper = new HelperForm();
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
