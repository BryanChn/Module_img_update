<?php

class AdminS2iImageController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
    }

    public function postProcess()
    {
        $action = Tools::getValue('action');

        if ($action === 'delete' && Tools::getValue('id')) {
            $this->handleDeleteSection();
        } elseif (Tools::isSubmit('submitCreateSection')) {
            $this->handleCreateSection();
        }

        parent::postProcess();
    }

    private function handleDeleteSection()
    {
        $id = (int)Tools::getValue('id');
        if ($id) {
            Db::getInstance()->delete('s2i_sections', 'id_s2i_section = ' . (int) $id);
            Db::getInstance()->delete('s2i_section_details', 'id_s2i_section = ' . (int) $id);
            Db::getInstance()->delete('s2i_section_details_lang', 'id_s2i_section = ' . (int) $id);
            $this->confirmations[] = $this->trans('La section a été supprimée avec succès.', [], 'Modules.S2iUpdateImg.Admin');
        } else {
            $this->errors[] = $this->trans('Erreur : Objet introuvable pour la suppression.', [], 'Modules.S2iUpdateImg.Admin');
        }
    }

    private function handleCreateSection()
    {
        $section = new Sections();
        $section->name = Tools::getValue('name');
        $section->active = Tools::getValue('active') ? 1 : 0;
        $section->slider = Tools::getValue('slider') ? 1 : 0;
        $section->speed = $section->slider ? Tools::getValue('speed') : null;

        if (!$section->add()) {
            $this->errors[] = $this->module->l('Error while creating section.');
            return;
        }

        $section_id = $section->id;

        $detail = new SectionDetails();
        $detail->id_s2i_section = $section_id;
        $detail->only_title = Tools::getValue('only_title');
        $detail->active = Tools::getValue('active');
        if (!$detail->add()) {
            $this->errors[] = $this->module->l('Error while creating section details.');
            return;
        }

        $detail_id = $detail->id;

        $languages = Language::getLanguages();
        foreach ($languages as $lang) {
            $detail_lang = new SectionDetailsLang();
            $detail_lang->id_s2i_detail = $detail_id;
            $detail_lang->id_lang = $lang['id_lang'];
            $detail_lang->title = Tools::getValue('title_' . $lang['id_lang']);
            $detail_lang->legend = Tools::getValue('legend_' . $lang['id_lang']);
            $detail_lang->url = Tools::getValue('url_' . $lang['id_lang']);

            $this->uploadImageForLang($detail_lang, 'image_' . $lang['id_lang'], $section->name . '-' . $section_id);
            if (Tools::getValue('image_mobile')) {
                $this->uploadImageForLang($detail_lang, 'image_mobile_' . $lang['id_lang'], $section->name . '-' . $section_id . '-m');
            }

            if (!$detail_lang->add()) {
                $this->errors[] = $this->module->l('Error while saving section details in each language.');
            }
        }
    }

    private function uploadImageForLang($detail_lang, $image_input_name, $filename_prefix)
    {
        $image = $_FILES[$image_input_name];
        if ($image['size'] > 0) {
            $filename = $filename_prefix . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
            move_uploaded_file($image['tmp_name'], _PS_IMG_DIR_ . $filename);
            $detail_lang->image = $filename;
        }
    }
}
