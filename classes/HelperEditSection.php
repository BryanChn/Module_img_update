<?php

class HelperEditSection
{
    public static function renderEditForm($module, $id_section)
    {
        $section = new Section($id_section);

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->module = $module;
        $helper->name_controller = 'AdminS2iImage';
        $helper->identifier = 'id_section';
        $helper->submit_action = 'submit_update_section';
        $helper->currentIndex = Context::getContext()->link->getAdminLink('AdminS2iImage');
        $helper->token = Tools::getAdminTokenLite('AdminS2iImage');

        $helper->fields_value = [
            'id_section' => $id_section,
            'name' => $section->name,
            'active' => $section->active,
            'is_slider' => $section->is_slider,
            'speed' => $section->speed,
            'position' => $section->position,
            'hook_location' => $section->hook_location
        ];

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $module->l('Paramètres de la section'),
                    'icon' => 'icon-cogs'
                ],
                'input' => [
                    [
                        'type' => 'hidden',
                        'name' => 'id_section'
                    ],
                    [
                        'type' => 'text',
                        'label' => $module->l('Nom'),
                        'name' => 'name',
                        'required' => true
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
                        'name' => 'is_slider',
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
                        'suffix' => 'ms'
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
