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


        if ($action === 'delete' && Tools::getValue('id')) {
            $this->handleDeleteSection();
        } elseif (Tools::isSubmit('submit_create_section')) {
            $this->handleCreateSection();
        } elseif (Tools::isSubmit('submit_update_section')) {
            $this->handleUpdateSection();
        }
    }



    private function handleUpdateSection()
    {
        $id_s2i_section = (int)Tools::getValue('id_s2i_section');
        $section = new Sections($id_s2i_section);
        $section->name = Tools::getValue('name');
        $section->active = Tools::getValue('active');
        $section->slider = Tools::getValue('slider');
        $section->speed = Tools::getValue('speed');

        if (!$section->update()) {
            $this->errors[] = $this->trans('Failed to update section');
        }

        // Mise à jour des détails multilingues
        $languages = Language::getLanguages();
        foreach ($languages as $lang) {
            $detail_lang = new SectionDetailsLang($section->id, $lang['id_lang']);
            $detail_lang->title = Tools::getValue('title_' . $lang['id_lang']);
            $detail_lang->legend = Tools::getValue('legend_' . $lang['id_lang']);

            if (!$detail_lang->update()) {
                $this->errors[] = $this->trans('Failed to update multilingual fields');
            }
        }

        // Redirection en cas de succès
        if (empty($this->errors)) {
            $this->confirmations[] = $this->trans('Section updated successfully');
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

    public function handleCreateSection()
    {
        // Création de la section principale
        $section = new Sections();
        $section->name = Tools::getValue('name');
        $section->active = (int) Tools::getValue('active');
        $section->slider = (int) Tools::getValue('slider');
        $section->speed = (int) Tools::getValue('speed');

        // Sauvegarde de la section principale
        if ($section->add()) { // `add()` crée un nouvel enregistrement et génère un ID pour la section
            $section_id = $section->id;

            // Insertion dans `ps_s2i_section_details`
            $sectionDetails = [
                'id_s2i_section' => $section_id,
                'active' => (int) Tools::getValue('active'),
                'only_title' => (int) Tools::getValue('only_title'),
                'image_is_mobile' => (int) Tools::getValue('image_mobile_enabled'),
            ];
            Db::getInstance()->insert('s2i_section_details', $sectionDetails);
            $section_detail_id = Db::getInstance()->Insert_ID();

            // Insertion des données multilingues dans `ps_s2i_section_details_lang`
            $languages = Language::getLanguages();
            foreach ($languages as $lang) {
                $lang_id = (int) $lang['id_lang'];
                $title = Tools::getValue('title_' . $lang_id);
                $legend = Tools::getValue('legend_' . $lang_id);
                $url = Tools::getValue('url_' . $lang_id);


                //gestion image 
                $imagePath = $this->handleImageUpload($lang_id);

                // Insertion dans `ps_s2i_section_details_lang`
                $sectionDetailsLang = [
                    'id_s2i_detail' => $section_detail_id,
                    'id_lang' => $lang_id,
                    'title' => $title,
                    'legend' => $legend,
                    'url' => $url,
                    'image' => $imagePath,

                ];
                Db::getInstance()->insert('s2i_section_details_lang', $sectionDetailsLang);
            }
        }
    }

    public function handleImageUpload($lang_id)
    {
        // Vérifie si le champ de l'image existe et contient un fichier
        if (isset($_FILES['image_' . $lang_id]) && !empty($_FILES['image_' . $lang_id]['name'])) {
            // Récupère l'extension du fichier
            $extension = pathinfo($_FILES['image_' . $lang_id]['name'], PATHINFO_EXTENSION);

            // Définition du dossier d'upload
            $uploadDir = _PS_IMG_DIR_ . 's2i_update_img/';

            // Création du dossier si inexistant
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Chemin final de l'image (garde le nom d'origine)
            $uploadPath = $uploadDir . basename($_FILES['image_' . $lang_id]['name']);

            // Déplace l'image dans le dossier
            if (move_uploaded_file($_FILES['image_' . $lang_id]['tmp_name'], $uploadPath)) {
                // Renvoie le chemin de l'image pour l'enregistrer en base de données ou autre
                return 's2i_update_img/' . basename($_FILES['image_' . $lang_id]['name']);
            } else {
                // Affiche une erreur si le déplacement échoue
                $this->errors[] = $this->trans('Erreur lors du téléchargement de l\'image.');
                return false;
            }
        }

        return null;
    }
}
