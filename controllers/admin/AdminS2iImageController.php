<?php


require_once _PS_MODULE_DIR_ . 's2i_update_img/classes/Section.php';
require_once _PS_MODULE_DIR_ . 's2i_update_img/classes/Slide.php';
require_once _PS_MODULE_DIR_ . 's2i_update_img/classes/SlideLang.php';
require_once _PS_MODULE_DIR_ . 's2i_update_img/classes/HelperEditSection.php';
require_once _PS_MODULE_DIR_ . 's2i_update_img/classes/HelperListSection.php';
class AdminS2iImageController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->table = 's2i_sections';
        $this->identifier = 'id_section';
        $this->className = 'Section';
        $this->allow_export = false;
    }
    public function init()
    {
        parent::init();
    }

    // fonction pour afficher les pages 
    public function initContent()
    {
        parent::initContent();

        // Déterminer quelle vue afficher en fonction des paramètres
        if (Tools::isSubmit('edits2i_section_slides') || Tools::getValue('id_slide')) {
            // Ajout de la vérification de id_slide
            $this->content .= $this->renderSlideEditForm();
        } elseif (Tools::getValue('id_section')) {
            $this->content .= $this->renderSectionPanel();
        } else {
            $this->content .= $this->renderConfiguration();
        }

        $this->context->smarty->assign([
            'content' => $this->content
        ]);
    }

    protected function renderSlideEditForm()
    {
        $id_slide = (int)Tools::getValue('id_slide');
        $id_section = (int)Tools::getValue('id_section');

        $editForm = HelperEditSection::renderEditSlideForm($this->module, $id_slide);

        $this->context->smarty->assign([
            'editForm' => $editForm,
            'id_section' => $id_section,
            'id_slide' => $id_slide
        ]);

        return $this->module->display($this->module->getLocalPath(), 'views/templates/admin/edit_slide.tpl');
    }


    // fonction pour afficher la liste des slides
    protected function renderSectionPanel()
    {
        $id_section = (int)Tools::getValue('id_section');

        // Vérification de la section
        $section = new Section($id_section);
        if (!Validate::isLoadedObject($section)) {
            $this->errors[] = $this->trans('Section introuvable');
            return false;
        }

        // Préparation des données
        $editForm = HelperEditSection::renderEditForm($this->module, $id_section);
        $slideManager = new SlideManager($this->module, $id_section);
        $slidesList = $slideManager->renderSlidesList();

        // Ajout de id_section dans l'assignation
        $this->context->smarty->assign([
            'editForm' => $editForm,
            'slidesList' => $slidesList,
            'id_section' => $id_section  // Ajout de cette ligne
        ]);

        return $this->module->display($this->module->getLocalPath(), 'views/templates/admin/panel_section_slide.tpl');
    }


    protected function renderConfiguration()
    {

        return $this->module->display($this->module->getLocalPath(), 'views/templates/admin/configuration.tpl');
    }

    // fonction pour gérer les mises à jour
    public function processUpdate()
    {
        // Gérer les mises à jour ici
        if (Tools::isSubmit('submit_update_slide')) {
            // Logique de mise à jour du slide
        } elseif (Tools::isSubmit('submit_update_section')) {
            // Logique de mise à jour de la section
        }
    }
    // fonction pour gérer les soumissions
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
        $section = new Section();
        $section->name = Tools::getValue('name');
        $section->active = (int) Tools::getValue('active');
        $section->is_slider = (int) Tools::getValue('slider');
        $section->speed = (int) Tools::getValue('speed');
        $section->position = (int) Tools::getValue('position', 0);
        $section->hook_location = Tools::getValue('hook_location');

        // Vérification du nom unique
        $sectionName = Tools::getValue('name');
        $existingSection = Db::getInstance()->getValue(
            'SELECT id_section FROM ' . _DB_PREFIX_ . 's2i_sections WHERE name = "' . pSQL($sectionName) . '"'
        );
        if ($existingSection) {
            $this->errors[] = $this->trans('Une section avec ce nom existe déjà.', [], 'Modules.S2iUpdateImg.Admin');
            return false;
        }

        if ($section->add()) {
            $section_id = $section->id;

            // Création de la diapositive
            $slide = new Slide();
            $slide->id_section = $section_id;
            $slide->active = (int) Tools::getValue('active');
            $slide->only_title = (int) Tools::getValue('only_title');
            $slide->title_hide = (int) Tools::getValue('title_hide', 0);
            $slide->image_is_mobile = (int) Tools::getValue('image_mobile_enabled');
            $slide->position = 0;

            if ($slide->add()) {
                // Ajout des traductions
                foreach (Language::getLanguages() as $lang) {
                    $slideLang = new SlideLang();
                    $slideLang->id_slide = $slide->id;
                    $slideLang->id_lang = (int) $lang['id_lang'];
                    $slideLang->title = Tools::getValue('title_' . $lang['id_lang']);
                    $slideLang->legend = Tools::getValue('legend_' . $lang['id_lang']);
                    $slideLang->url = Tools::getValue('url_' . $lang['id_lang']);

                    // Gestion des images
                    if (isset($_FILES['image_' . $lang['id_lang']]) && !empty($_FILES['image_' . $lang['id_lang']]['name'])) {
                        $imagePath = $this->handleImageUpload($section->name, $lang['id_lang']);
                        if ($imagePath) {
                            $slideLang->image = $imagePath;
                        }
                    }

                    if (isset($_FILES['image_mobile_' . $lang['id_lang']]) && !empty($_FILES['image_mobile_' . $lang['id_lang']]['name'])) {
                        $imageMobilePath = $this->handleImageUpload($section->name, $lang['id_lang'], true);
                        if ($imageMobilePath) {
                            $slideLang->image_mobile = $imageMobilePath;
                        }
                    }

                    if (!$slideLang->add()) {
                        $this->errors[] = $this->trans('Erreur lors de l\'ajout des traductions', [], 'Modules.S2iUpdateImg.Admin');
                    }
                }
            }

            if (empty($this->errors)) {
                $this->context->cookie->__set('s2i_success_message', 'Section créée avec succès');
                $this->context->cookie->write();
            }
        }
    }

    protected function handleUpdateSection()
    {
        $id_section = (int)Tools::getValue('id_section');
        $section = new Section($id_section);

        // Mise à jour de la section
        $section->name = Tools::getValue('name');
        $section->active = (int)Tools::getValue('active');
        $section->is_slider = (int)Tools::getValue('slider');
        $section->speed = (int)Tools::getValue('speed');
        $section->position = (int)Tools::getValue('position', 0);
        $section->hook_location = Tools::getValue('hook_location');

        if (!$section->update()) {
            $this->errors[] = $this->trans('Erreur lors de la mise à jour de la section');
            return false;
        }

        // Mise à jour ou création de la diapositive
        $slide = Slide::getBySection($id_section);
        if (!$slide) {
            $slide = new Slide();
            $slide->id_section = $id_section;
        }

        $slide->active = (int)Tools::getValue('active');
        $slide->only_title = (int)Tools::getValue('only_title');
        $slide->title_hide = (int)Tools::getValue('title_hide', 0);
        $slide->image_is_mobile = (int)Tools::getValue('image_mobile_enabled');

        if (!$slide->save()) {
            $this->errors[] = $this->trans('Erreur lors de la mise à jour de la diapositive');
            return false;
        }

        // Mise à jour des traductions
        foreach (Language::getLanguages(true) as $lang) {
            $lang_id = (int)$lang['id_lang'];
            $slideLang = SlideLang::getBySlideAndLang($slide->id, $lang_id);

            if (!$slideLang) {
                $slideLang = new SlideLang();
                $slideLang->id_slide = $slide->id;
                $slideLang->id_lang = $lang_id;
            }

            $slideLang->title = Tools::getValue('title_' . $lang_id);
            $slideLang->legend = Tools::getValue('legend_' . $lang_id);
            $slideLang->url = Tools::getValue('url_' . $lang_id);

            // Gestion de l'upload d'image
            if (isset($_FILES['image_' . $lang_id]) && !empty($_FILES['image_' . $lang_id]['name'])) {
                $imagePath = $this->handleImageUpload($section->name, $lang_id);
                if ($imagePath) {
                    $slideLang->image = $imagePath;
                }
            }

            if (!$slideLang->save()) {
                $this->errors[] = $this->trans('Erreur lors de la mise à jour des traductions');
            }
        }

        if (empty($this->errors)) {
            $this->context->cookie->__set('s2i_success_message', 'Section mise à jour avec succès');
            $this->context->cookie->write();
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->module->name);
        }
    }


    protected function handleImageUpload($section_name, $id_lang, $isMobile = false)
    {
        // Déterminer quel fichier utiliser en fonction du type d'image
        $fileKey = $isMobile ? 'image_mobile_' . $id_lang : 'image_' . $id_lang;

        if (!isset($_FILES[$fileKey]) || empty($_FILES[$fileKey]['name'])) {
            return false;
        }

        // Sécurisation du nom de la section pour le nom de fichier
        $safeName = Tools::str2url($section_name);

        // Récupération de l'extension
        $extension = pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION);

        // Gestion du suffixe mobile
        $suffix = $isMobile ? '-m-' : '-';

        // Construction du nom du fichier
        $imageName = $safeName . $suffix . $id_lang . '.' . $extension;

        // Définition du chemin de destination
        $uploadDir = _PS_IMG_DIR_ . 's2i_update_img/';

        // Création du dossier si nécessaire
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $uploadPath = $uploadDir . $imageName;

        // Upload du fichier
        if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $uploadPath)) {
            return 's2i_update_img/' . $imageName;
        }

        $this->errors[] = $this->trans('Erreur lors du téléchargement de l\'image pour la langue ') . $id_lang;
        return false;
    }
}
