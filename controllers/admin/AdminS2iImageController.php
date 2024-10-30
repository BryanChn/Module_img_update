<?php

require_once _PS_MODULE_DIR_ . 's2i_update_img/classes/Section.php';
require_once _PS_MODULE_DIR_ . 's2i_update_img/classes/SectionDetails.php';
require_once _PS_MODULE_DIR_ . 's2i_update_img/classes/SectionDetailsLang.php';
require_once _PS_MODULE_DIR_ . 's2i_update_img/classes/HelperEditSection.php';

class AdminS2iImageController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
    }

    public function postProcess()
    {

        $this->confirmations[] = $this->trans('postProcess called.', [], 'Modules.S2iUpdateImg.Admin');
        $action = Tools::getValue('action');
        var_dump($action);

        if ($action === 'delete' && Tools::getValue('id')) {
            $this->handleDeleteSection();
        } elseif (Tools::isSubmit('submit_create_section')) {
            $this->handleCreateSection();
        } elseif (Tools::isSubmit('submit_update_section')) {
            $this->handleUpdateSection();
        } elseif (Tools::getValue('action') === 'edit' && Tools::getValue('id_s2i_section')) {
            return HelperEditSection::renderEditForm($this, (int)Tools::getValue('id_s2i_section'));
        }


        parent::postProcess();
    }

    public function renderEditList($id_s2i_section)
    {
        // Recherchez la section et ses détails
        $section = new Sections($id_s2i_section);
        $details = SectionDetails::getBySectionId($id_s2i_section);
        $languages = Language::getLanguages();

        $sectionDetailsLang = [];
        foreach ($languages as $lang) {
            $sectionDetailsLang[$lang['id_lang']] = SectionDetailsLang::getByDetailAndLang($details['id_s2i_detail'], $lang['id_lang']);
        }

        // Assignez les données à Smarty pour l'affichage
        $this->context->smarty->assign([
            'section' => $section,
            'details' => $details,
            'sectionDetailsLang' => $sectionDetailsLang,
            'languages' => $languages,
            'currentIndex' => AdminController::$currentIndex,
            'token' => Tools::getAdminTokenLite('AdminModules')
        ]);

        // Retournez le rendu du template d'édition
        return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/edit_section.tpl');
    }

    private function handleUpdateSection()
    {
        $id_section = (int)Tools::getValue('id_section');
        $section = new Sections($id_section);
        $section->name = Tools::getValue('name');
        $section->active = Tools::getValue('active') ? 1 : 0;
        $section->slider = Tools::getValue('slider') ? 1 : 0;
        $section->speed = Tools::getValue('speed');

        if (!$section->update()) {
            $this->errors[] = $this->module->l('Error while updating section.');
        }

        $detail = SectionDetails::getBySectionId($id_section);
        $detail->only_title = Tools::getValue('only_title');
        $detail->active = Tools::getValue('active');

        if (!$detail->update()) {
            $this->errors[] = $this->module->l('Error while updating section details.');
        }

        $languages = Language::getLanguages();
        foreach ($languages as $lang) {
            $detail_lang = new SectionDetailsLang($detail->id, $lang['id_lang']);
            $detail_lang->title = Tools::getValue('title_' . $lang['id_lang']);
            $detail_lang->legend = Tools::getValue('legend_' . $lang['id_lang']);
            $detail_lang->url = Tools::getValue('url_' . $lang['id_lang']);

            // Gestion de l'image si une nouvelle est uploadée
            $this->uploadImageForLang($detail_lang, 'image_' . $lang['id_lang'], $section->name . '-' . $id_section);

            if (!$detail_lang->update()) {
                $this->errors[] = $this->module->l('Error while updating section details in each language.');
            }
        }

        if (empty($this->errors)) {
            $this->confirmations[] = $this->module->l('Section mise à jour avec succès.');
            Tools::redirectAdmin(AdminController::$currentIndex . '&configure=' . $this->module->name . '&token=' . Tools::getAdminTokenLite('AdminModules'));
        }
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

        // Ajouter la section en base de données pour générer un ID
        if (!$section->add()) {  // Ajout de la section en base
            $this->errors[] = $this->module->l('Error while creating section.');
            return; // Arrêter si l'ajout échoue
        }

        $section_id = $section->id; // Récupérer l'ID généré de la section

        $detail = new SectionDetails();
        $detail->id_s2i_section = $section_id; // Utiliser l'ID de la section générée
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

            // Gestion de l'image pour chaque langue
            $this->uploadImageForLang($detail_lang, 'image_' . $lang['id_lang'], $section->name . '-' . $section_id);
            if (Tools::getValue('image_mobile')) {
                $this->uploadImageForLang($detail_lang, 'image_mobile_' . $lang['id_lang'], $section->name . '-' . $section_id . '-m');
            }

            if (!$detail_lang->add()) {
                $this->errors[] = $this->module->l('Error while saving section details in each language.');
            }
        }

        if (empty($this->errors)) {
            $this->confirmations[] = $this->module->l('Section créée avec succès.');
            Tools::redirectAdmin(AdminController::$currentIndex . '&configure=' . $this->module->name . '&token=' . Tools::getAdminTokenLite('AdminModules'));
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
