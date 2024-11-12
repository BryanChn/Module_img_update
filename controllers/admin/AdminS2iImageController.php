<?php


require_once _PS_MODULE_DIR_ . 's2i_update_img/classes/Section.php';
require_once _PS_MODULE_DIR_ . 's2i_update_img/classes/Slide.php';
require_once _PS_MODULE_DIR_ . 's2i_update_img/classes/SlideLang.php';
require_once _PS_MODULE_DIR_ . 's2i_update_img/classes/HelperEditSection.php';
require_once _PS_MODULE_DIR_ . 's2i_update_img/classes/HelperListSection.php';
require_once _PS_MODULE_DIR_ . 's2i_update_img/classes/Form_add_slide.php';
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
    public function postProcess()
    {
        if (Tools::isSubmit('submit_create_section')) {
            if ($this->handleCreateSection()) {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . "&configure=s2i_update_img");
                return;
            }
            return;
        }

        if (Tools::isSubmit('submit_update_section_only')) {
            $this->handleUpdateSectionOnly();

            return;
        }

        if (Tools::isSubmit('submit_add_slide')) {
            if ($this->handleAddSlide()) {
                $id_section = (int)Tools::getValue('id_section');
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminS2iImage') . '&id_section=' . $id_section);
            }
            return;
        }

        if (Tools::isSubmit('submit_update_slide')) {
            if ($this->handleUpdateSlide()) {
                $id_section = (int)Tools::getValue('id_section');
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminS2iImage', true) . '&id_section=' . $id_section);
            }
            return;
        }

        parent::postProcess();
    }


    // fonction pour afficher les pages 
    public function initContent()
    {
        parent::initContent();

        // Vérifier d'abord si on veut ajouter un slide
        if (Tools::getValue('add_slide')) {
            $this->content = $this->renderAddSlideForm();
        }
        // Ensuite vérifier les autres cas
        elseif (Tools::isSubmit('edits2i_section_slides') || Tools::getValue('id_slide')) {
            $this->content .= $this->renderSlideEditForm();
        } elseif (Tools::getValue('id_section')) {
            $this->content .= $this->renderSectionPanel();
        }

        $this->context->smarty->assign([
            'content' => $this->content
        ]);
    }

    public function ajaxProcessUpdateSlidesPosition()
    {
        $order = Tools::getValue('order');
        if ($order && is_array($order)) {
            foreach ($order as $position => $id_slide) {
                $result = Db::getInstance()->update(
                    's2i_section_slides',
                    ['position' => (int)$position],
                    'id_slide = ' . (int)$id_slide
                );
                if (!$result) {
                    die(json_encode(['error' => true]));
                }
            }
            die(json_encode(['success' => true]));
        }
        die(json_encode(['error' => true]));
    }

    protected function renderSlideEditForm()
    {
        $id_slide = (int)Tools::getValue('id_slide');



        // Récupérer les données du slide
        $slide = new Slide($id_slide);
        $id_section = $slide->id_section;
        $slideLangs = [];

        // Récupérer les traductions pour chaque langue
        foreach (Language::getLanguages(true) as $lang) {
            $slideLang = new SlideLang();
            $existingLang = $slideLang->getBySlideAndLang($id_slide, $lang['id_lang']);
            if ($existingLang) {
                $slideLangs[$lang['id_lang']] = $existingLang;
            }
        }

        $editForm = HelperEditSection::renderEditSlideForm($this->module, $id_slide, $slide, $slideLangs);

        $this->context->smarty->assign([
            'editForm' => $editForm,
            'id_section' => $id_section,
            'id_slide' => $id_slide,
            'slide' => $slide,
            'slideLangs' => $slideLangs
        ]);

        return $this->module->display($this->module->getLocalPath(), 'views/templates/admin/edit_slide.tpl');
    }


    protected function renderSectionPanel()
    {
        $id_section = (int)Tools::getValue('id_section');
        $id_slide = (int)Tools::getValue('id_slide');
        // Vérification de la section
        $section = new Section($id_section);
        if (!Validate::isLoadedObject($section)) {
            $this->errors[] = $this->trans('Section introuvable');
            return false;
        }

        // Préparation des données
        $editForm = HelperEditSection::renderEditForm($this->module, $id_section, $id_slide);
        $slideManager = new SlideManager($this->module, $id_section);
        $slidesList = $slideManager->renderSlidesList();

        // Ajout de id_section dans l'assignation
        $this->context->smarty->assign([
            'editForm' => $editForm,
            'slidesList' => $slidesList,
            'id_section' => $id_section
        ]);

        return $this->module->display($this->module->getLocalPath(), 'views/templates/admin/panel_section_slide.tpl');
    }

    protected function renderAddSlideForm()
    {
        $id_section = (int)Tools::getValue('id_section');

        // Vérification de la section avant de continuer
        $section = new Section($id_section);
        if (!Validate::isLoadedObject($section)) {
            $this->errors[] = $this->trans('Section invalide');
            return false;
        }

        $form = new Form_add_slide();
        $add_slide = $form->renderFormAddSlide($this->module, $id_section);

        $this->context->smarty->assign([
            'add_slide' => $add_slide,
            'id_section' => $id_section,
            'section' => $section
        ]);

        return $this->module->display($this->module->getLocalPath(), 'views/templates/admin/add_slide.tpl');
    }

    public function processUpdate()
    {
        // Gérer les mises à jour ici
        if (Tools::isSubmit('submit_update_slide')) {
            // Logique de mise à jour du slide
        } elseif (Tools::isSubmit('submit_update_section')) {
            // Logique de mise à jour de la section
        }
    }



    public function handleCreateSection()
    {
        // Création de la section principale
        $section = new Section();
        $section->name = Tools::getValue('name');
        $section->active = (int)Tools::getValue('active');
        $section->is_slider = (int)Tools::getValue('is_slider');
        $section->speed = (int)Tools::getValue('speed', 5000);
        $section->position = (int)Tools::getValue('position', 0);
        $section->hook_location = Tools::getValue('hook_location');

        // Vérification du nom unique
        $sectionName = Tools::getValue('name');
        if (empty($sectionName)) {
            $this->errors[] = $this->trans('Le nom de la section est requis');
            return false;
        }

        $existingSection = Db::getInstance()->getValue(
            'SELECT id_section FROM ' . _DB_PREFIX_ . 's2i_sections WHERE name = "' . pSQL($sectionName) . '"'
        );

        if ($existingSection) {
            $this->errors[] = $this->trans('Une section avec ce nom existe déjà.');
            return false;
        }

        if (!$section->add()) {
            $this->errors[] = $this->trans('Erreur lors de la création de la section');
            return false;
        }

        // Création du slide initial
        $slide = new Slide();
        $slide->id_section = $section->id;
        $slide->active = (int)Tools::getValue('active');
        $slide->only_title = (int)Tools::getValue('only_title');
        $slide->title_hide = (int)Tools::getValue('title_hide', 0);
        $slide->image_is_mobile = (int)Tools::getValue('image_is_mobile');
        $slide->position = 0;

        if (!$slide->add()) {
            $this->errors[] = $this->trans('Erreur lors de la création du slide');
            return false;
        }

        // Gestion des traductions pour le slide
        foreach (Language::getLanguages(true) as $lang) {
            $slideLang = new SlideLang();
            $slideLang->id_slide = $slide->id;
            $slideLang->id_lang = (int)$lang['id_lang'];
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

            // Gestion des images mobiles
            if (isset($_FILES['image_mobile_' . $lang['id_lang']]) && !empty($_FILES['image_mobile_' . $lang['id_lang']]['name'])) {
                $imagePath = $this->handleImageUpload($section->name, $lang['id_lang'], true);
                if ($imagePath) {
                    $slideLang->image_mobile = $imagePath;
                }
            }

            if (!$slideLang->add()) {
                $this->errors[] = $this->trans('Erreur lors de l\'ajout des traductions');
                return false;
            }
        }

        $this->context->cookie->__set('s2i_success_message', 'Section créée avec succès');
        $this->context->cookie->write();

        return true;
    }


    protected function handleUpdateSectionOnly()
    {
        $id_section = (int)Tools::getValue('id_section');
        $section = new Section($id_section);

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

        $this->context->cookie->__set('s2i_success_message', 'Section mise à jour avec succès');
        $this->context->cookie->write();
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminS2iImage') . '&id_section=' . $id_section);
        return true;
    }

    protected function handleAddSlide()
    {
        $id_section = (int)Tools::getValue('id_section');

        // Récupération de la section pour le nom
        $section = new Section($id_section);


        if (!Validate::isLoadedObject($section)) {
            $this->errors[] = $this->trans('Section invalide');
            return false;
        }

        $slide = new Slide();
        $slide->id_section = $id_section;
        $slide->active = (int)Tools::getValue('active');
        $slide->only_title = (int)Tools::getValue('only_title');
        $slide->title_hide = (int)Tools::getValue('title_hide', 0);
        $slide->image_is_mobile = (int)Tools::getValue('image_mobile_enabled');
        $slide->position = 0;

        if (!$slide->add()) {
            $this->errors[] = $this->trans('Erreur lors de la création du slide');
            return false;
        }

        // Gestion des traductions
        foreach (Language::getLanguages(true) as $lang) {
            $slideLang = new SlideLang();
            $slideLang->id_slide = $slide->id;
            $slideLang->id_lang = (int)$lang['id_lang'];
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
                $this->errors[] = $this->trans('Erreur lors de l\'ajout des traductions');
            }
        }

        return empty($this->errors);
    }

    protected function handleUpdateSlide()
    {
        $id_slide = (int)Tools::getValue('id_slide');
        $id_section = (int)Tools::getValue('id_section');
        $section = new Section($id_section);
        if (!Validate::isLoadedObject($section)) {
            $this->errors[] = $this->trans('Section invalide');
            return false;
        }

        if (!$id_slide || !$id_section) {
            $this->errors[] = $this->trans('IDs manquants');
            return false;
        }

        $slide = new Slide($id_slide);
        if (!Validate::isLoadedObject($slide)) {
            $this->errors[] = $this->trans('Slide invalide');
            return false;
        }

        // Mise à jour des champs de base
        $slide->active = (int)Tools::getValue('active');
        $slide->only_title = (int)Tools::getValue('only_title');
        $slide->title_hide = (int)Tools::getValue('title_hide');
        $slide->image_is_mobile = (int)Tools::getValue('image_is_mobile');

        if (!$slide->update()) {
            $this->errors[] = $this->trans('Erreur lors de la mise à jour du slide');
            return false;
        }

        // Mise à jour des traductions
        foreach (Language::getLanguages(true) as $lang) {
            $id_lang = (int)$lang['id_lang'];
            $slideLang = new SlideLang();
            $existingLang = $slideLang->getBySlideAndLang($slide->id, $id_lang);

            if ($existingLang) {
                $slideLang = new SlideLang($existingLang['id_slide_lang']);
            } else {
                $slideLang->id_slide = $slide->id;
                $slideLang->id_lang = $id_lang;
            }

            // Ne mettre à jour que si les valeurs sont soumises
            if (Tools::getValue('title_' . $id_lang) !== false) {
                $slideLang->title = Tools::getValue('title_' . $id_lang);
            }
            if (Tools::getValue('legend_' . $id_lang) !== false) {
                $slideLang->legend = Tools::getValue('legend_' . $id_lang);
            }
            if (Tools::getValue('url_' . $id_lang) !== false) {
                $slideLang->url = Tools::getValue('url_' . $id_lang);
            }

            // Ne traiter l'image que si un nouveau fichier est uploadé
            if (isset($_FILES['image_' . $id_lang]) && !empty($_FILES['image_' . $id_lang]['name'])) {
                $imagePath = $this->handleImageUpload($section->name, $id_lang);
                if ($imagePath) {
                    $slideLang->image = $imagePath;
                }
            }

            if (isset($_FILES['image_mobile_' . $id_lang]) && !empty($_FILES['image_mobile_' . $id_lang]['name'])) {
                $imageMobilePath = $this->handleImageUpload($section->name, $id_lang, true);
                if ($imageMobilePath) {
                    $slideLang->image_mobile = $imageMobilePath;
                }
            }

            if ($existingLang) {
                if (!$slideLang->update()) {
                    $this->errors[] = $this->trans('Erreur lors de la mise à jour des traductions pour la langue ') . $lang['name'];
                    return false;
                }
            } else {
                if (!$slideLang->add()) {
                    $this->errors[] = $this->trans('Erreur lors de l\'ajout des traductions pour la langue ') . $lang['name'];
                    return false;
                }
            }
        }

        return true;
    }

    protected function handleImageUpload($section_name, $id_lang, $isMobile = false)
    {
        $fileKey = $isMobile ? 'image_mobile_' . $id_lang : 'image_' . $id_lang;

        if (!isset($_FILES[$fileKey]) || empty($_FILES[$fileKey]['name'])) {
            return false;
        }

        $safeName = Tools::str2url($section_name);
        $extension = pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION);
        $suffix = $isMobile ? '-m-' : '-';
        $uploadDir = _PS_IMG_DIR_ . 's2i_update_img/';

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Trouver le prochain numéro d'incrémentation
        $files = glob($uploadDir . $safeName . $suffix . $id_lang . '-*.' . $extension);
        $maxNumber = 0;
        foreach ($files as $file) {
            if (preg_match('/-(\d+)\.' . preg_quote($extension, '/') . '$/', $file, $matches)) {
                $maxNumber = max($maxNumber, (int)$matches[1]);
            }
        }
        $nextNumber = str_pad($maxNumber + 1, 3, '0', STR_PAD_LEFT);

        $imageName = $safeName . $suffix . $id_lang . '-' . $nextNumber . '.' . $extension;
        $uploadPath = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $uploadPath)) {
            return 's2i_update_img/' . $imageName;
        }

        $this->errors[] = $this->trans('Erreur lors du téléchargement de l\'image pour la langue ') . $id_lang;
        return false;
    }
}
