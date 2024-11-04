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
        $this->bootstrap = true;
        $this->table = 's2i_sections';
        $this->identifier = 'id_s2i_section';
        $this->className = 'Sections';
        $this->allow_export = false;
    }
    public function init()
    {
        parent::init();
    }

    public function initProcess()
    {
        parent::initProcess();
        if (Tools::isSubmit('delete' . $this->table)) {
            $this->action = 'delete';
        } elseif (Tools::isSubmit('update' . $this->table)) {
            $this->action = 'edit';
        }
    }




    public function initContent()
    {
        parent::initContent();
    }

    public function renderForm()
    {
        $id_s2i_section = Tools::getValue('id_s2i_section');
        $editForm = HelperEditSection::renderEditForm($this->module, $id_s2i_section);
        $this->context->smarty->assign([
            'editForm' => $editForm
        ]);


        return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/edit_section.tpl');
    }


    public function postProcess()
    {
        if (Tools::isSubmit('submit_update_section')) {
            $this->handleUpdateSection();
            return;
        }

        if (Tools::isSubmit('delete' . $this->table)) {
            if ($this->processDelete()) {
                // Stockage du message de confirmation dans la session
                $this->context->cookie->__set('s2i_success_message', 'La section a été supprimée.');
                $this->context->cookie->write();

                Tools::redirectAdmin(
                    $this->context->link->getAdminLink('AdminModules', true) .
                        '&configure=' . $this->module->name .
                        '&token=' . Tools::getAdminTokenLite('AdminModules')
                );
            }
        } elseif (Tools::isSubmit('submit_create_section')) {
            $this->handleCreateSection();
            Tools::redirectAdmin(
                $this->context->link->getAdminLink('AdminModules', true) .
                    '&configure=' . $this->module->name .
                    '&token=' . Tools::getAdminTokenLite('AdminModules')
            );
        } else {
            parent::postProcess();
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

        // verification en db si une section existe déjà avec le même nom
        $sectionName = Tools::getValue('name');
        $existingSection = Db::getInstance()->getValue(
            'SELECT id_s2i_section FROM ' . _DB_PREFIX_ . 's2i_sections WHERE name = "' . $sectionName . '"'
        );
        if ($existingSection) {
            // Si une section avec le même nom existe, on ajoute une erreur
            $this->errors[] = $this->trans('Une section avec ce nom existe déjà. Veuillez choisir un nom unique.', [], 'Modules.S2iUpdateImg.Admin');
            return false;
        }

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
                $imagePath = '';

                $safeName = Tools::str2url($section->name);

                // Gestion du nom de l'image avec le suffixe mobile si nécessaire
                $isMobileImage = (int) Tools::getValue('image_mobile_enabled') ? '-m-' : '-';
                if (isset($_FILES['image_' . $lang_id]) && !empty($_FILES['image_' . $lang_id]['name'])) {
                    $extension = pathinfo($_FILES['image_' . $lang_id]['name'], PATHINFO_EXTENSION);
                    $imageName = $safeName . $lang_id . $isMobileImage . '.' . $extension; // Format: NomSection+IDlang+(mobile suffix)+extension
                    $uploadDir = _PS_IMG_DIR_ . 's2i_update_img/';

                    // Création du dossier si inexistant
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $uploadPath = $uploadDir . $imageName;

                    if (move_uploaded_file($_FILES['image_' . $lang_id]['tmp_name'], $uploadPath)) {
                        $imagePath = 's2i_update_img/' . $imageName;
                    } else {
                        die('Erreur lors du déplacement du fichier : ' . $_FILES['image_' . $lang_id]['error']);
                    }
                } else {
                    $imagePath = ''; // Si aucune image n'est uploadée, on laisse ce champ vide.
                }

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
            if (empty($this->errors)) {
                // Stockage du message de confirmation dans la session
                $this->context->cookie->__set('s2i_success_message', 'La section a été créée avec succès !');
                $this->context->cookie->write();

                Tools::redirectAdmin(
                    $this->context->link->getAdminLink('AdminModules', true) .
                        '&configure=' . $this->module->name .
                        '&token=' . Tools::getAdminTokenLite('AdminModules')
                );
            }
        }
    }

    protected function handleUpdateSection()
    {
        $id_s2i_section = (int)Tools::getValue('id_s2i_section');
        $section = new Sections($id_s2i_section);

        // Mise à jour des données de base
        $section->name = Tools::getValue('name');
        $section->active = (int)Tools::getValue('active');
        $section->slider = (int)Tools::getValue('slider');
        $section->speed = (int)Tools::getValue('speed');

        if (!$section->update()) {
            $this->errors[] = $this->trans('Erreur lors de la mise à jour de la section');
            return false;
        }

        // Récupération ou création des détails
        $detailsData = SectionDetails::getBySectionId($id_s2i_section);
        if (!$detailsData) {
            // Si pas de détails, on crée un nouvel enregistrement
            $details = new SectionDetails();
            $details->id_s2i_section = $id_s2i_section;
            $details->add();
        } else {
            $details = new SectionDetails($detailsData['id_s2i_detail']);
        }

        // Mise à jour des détails
        $details->active = (int)Tools::getValue('active');
        $details->only_title = (int)Tools::getValue('only_title');

        if (!$details->save()) {
            $this->errors[] = $this->trans('Erreur lors de la mise à jour des détails');
            return false;
        }

        // Mise à jour des données multilingues
        foreach (Language::getLanguages(true) as $lang) {
            $lang_id = (int)$lang['id_lang'];
            $existingData = SectionDetailsLang::getByDetailAndLang($details->id, $lang_id);

            $updateData = [
                'title' => Tools::getValue('title_' . $lang_id),
                'legend' => Tools::getValue('legend_' . $lang_id),
                'url' => Tools::getValue('url_' . $lang_id)
            ];

            // Gestion de l'upload d'image
            if (isset($_FILES['image_' . $lang_id]) && !empty($_FILES['image_' . $lang_id]['name'])) {
                $imagePath = $this->handleImageUpload($section->name, $lang_id);
                if ($imagePath) {
                    $updateData['image'] = $imagePath;
                }
            }

            if ($existingData) {
                // Mise à jour des données existantes
                Db::getInstance()->update(
                    's2i_section_details_lang',
                    $updateData,
                    'id_s2i_detail = ' . (int)$details->id . ' AND id_lang = ' . (int)$lang_id
                );
            } else {
                // Insertion de nouvelles données
                $updateData['id_s2i_detail'] = (int)$details->id;
                $updateData['id_lang'] = (int)$lang_id;
                Db::getInstance()->insert('s2i_section_details_lang', $updateData);
            }
        }
        if (empty($this->errors)) {
            // Stockage du message de confirmation dans la session
            $this->context->cookie->__set('s2i_success_message', 'Section mise à jour avec succès');
            $this->context->cookie->write();

            Tools::redirectAdmin(
                $this->context->link->getAdminLink('AdminModules', true) .
                    '&configure=' . $this->module->name .
                    '&token=' . Tools::getAdminTokenLite('AdminModules')
            );
        }
    }


    protected function handleImageUpload($sectionName, $langId)
    {
        if (!isset($_FILES['image_' . $langId]) || empty($_FILES['image_' . $langId]['name'])) {
            return false;
        }

        // Sécurisation du nom de la section pour le nom de fichier
        $safeName = Tools::str2url($sectionName);

        // Récupération de l'extension
        $extension = pathinfo($_FILES['image_' . $langId]['name'], PATHINFO_EXTENSION);

        // Gestion du suffixe mobile
        $isMobileImage = (int)Tools::getValue('image_mobile_enabled') ? '-m-' : '-';

        // Construction du nom du fichier
        $imageName = $safeName . $langId . $isMobileImage . '.' . $extension;

        // Définition du chemin de destination
        $uploadDir = _PS_IMG_DIR_ . 's2i_update_img/';

        // Création du dossier si nécessaire
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $uploadPath = $uploadDir . $imageName;

        // Upload du fichier
        if (move_uploaded_file($_FILES['image_' . $langId]['tmp_name'], $uploadPath)) {
            return 's2i_update_img/' . $imageName;
        }

        $this->errors[] = $this->trans('Erreur lors du téléchargement de l\'image pour la langue ') . $langId;
        return false;
    }
}
