<?php


require_once _PS_MODULE_DIR_ . 's2i_gestionlist/classes/Section.php';
require_once _PS_MODULE_DIR_ . 's2i_gestionlist/classes/Slide.php';
require_once _PS_MODULE_DIR_ . 's2i_gestionlist/classes/SlideLang.php';
require_once _PS_MODULE_DIR_ . 's2i_gestionlist/classes/HelperEditSection.php';
require_once _PS_MODULE_DIR_ . 's2i_gestionlist/classes/HelperListSection.php';
require_once _PS_MODULE_DIR_ . 's2i_gestionlist/classes/Form_add_slide.php';
require_once _PS_MODULE_DIR_ . 's2i_gestionlist/classes/HookLocation.php';
class AdminS2iGestionlistController extends ModuleAdminController
{
    protected $positions_identifier = 'id_slide';
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
        if (Tools::isSubmit('delete' . $this->table)) {
            parent::postProcess();
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&configure=s2i_gestionlist');
            return;
        }

        if (Tools::isSubmit('submit_create_section')) {
            if ($this->handleCreateSection()) {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . "&configure=s2i_gestionlist");
                return;
            }
            return;
        }
        if (Tools::isSubmit('action') && Tools::getValue('action') == 'updatePositions') {
            $this->ajaxProcessUpdatePositions();
            exit;
        }

        if (Tools::isSubmit('submit_update_section_only')) {
            $this->handleUpdateSectionOnly();

            return;
        }

        if (Tools::isSubmit('submit_add_slide')) {
            if ($this->handleAddSlide()) {
                $id_section = (int)Tools::getValue('id_section');
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminS2iGestionlist') . '&id_section=' . $id_section);
            }
            return;
        }

        if (Tools::isSubmit('submit_update_slide')) {
            if ($this->handleUpdateSlide()) {
                $id_section = (int)Tools::getValue('id_section');
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminS2iGestionlist', true) . '&id_section=' . $id_section);
            }
            return;
        }
        if (Tools::isSubmit('deletes2i_section_slides')) {
            if ($this->handleDeleteSlide()) {
                $id_section = (int)Tools::getValue('id_section');
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminS2iGestionlist') . '&id_section=' . $id_section);
            }
            return;
        }

        parent::postProcess();
    }
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addJqueryUI('ui.sortable');
        Media::addJsDef([
            's2iGestionListConfig' => [
                'successMessage' => $this->trans('Positions mises à jour avec succès'),
                'errorMessage' => $this->trans('Erreur lors de la mise à jour des positions'),
                'updateUrl' => $this->context->link->getAdminLink('AdminS2iGestionlist'),
                'token' => Tools::getAdminTokenLite('AdminS2iGestionlist')
            ]
        ]);
    }

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

    public function ajaxProcessUpdatePositions()
    {
        if (!Tools::getIsset('token') || Tools::getValue('token') !== Tools::getAdminTokenLite('AdminS2iGestionlist')) {
            die(json_encode([
                'success' => false,
                'message' => $this->trans('Token invalide')
            ]));
        }

        $positions = Tools::getValue('positions');
        if (!$positions) {
            die(json_encode([
                'success' => false,
                'message' => $this->trans('Données de position manquantes')
            ]));
        }

        $positions = json_decode($positions, true);
        if (!is_array($positions)) {
            die(json_encode([
                'success' => false,
                'message' => $this->trans('Format de données invalide')
            ]));
        }

        $success = true;
        // Récupérer l'ID de la section pour la requête
        $first_slide = new Slide((int)$positions[0]['id']);
        $id_section = $first_slide->id_section;

        // Mettre à jour toutes les positions en une seule requête
        $cases = [];
        foreach ($positions as $position) {
            $cases[] = "WHEN " . (int)$position['id'] . " THEN " . ((int)$position['position'] + 1);
        }

        if (!empty($cases)) {
            $sql = "UPDATE `" . _DB_PREFIX_ . "s2i_section_slides` 
                    SET position = CASE id_slide " . implode(' ', $cases) . " END
                    WHERE id_section = " . (int)$id_section;

            $success = Db::getInstance()->execute($sql);
        }

        die(json_encode([
            'success' => $success,
            'message' => $success ?
                $this->trans('Positions mises à jour avec succès') :
                $this->trans('Erreur lors de la mise à jour des positions')
        ]));
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
        $id_hook_location = (int)Tools::getValue('id_hook_location');
        // Vérification de la section
        $section = new Section($id_section);
        if (!Validate::isLoadedObject($section)) {
            $this->errors[] = $this->trans('Section introuvable');
            return false;
        }

        // Préparation des données
        $editForm = HelperEditSection::renderEditForm($this->module, $id_section, $id_slide, $id_hook_location);
        $slideManager = new SlideManager($this->module, $id_section);
        $slidesList = $slideManager->renderSlidesList();
        $slides = SlidesLists::getSlidesList($id_section);
        $slides = array_map(function ($slide) {
            $id_lang = Context::getContext()->language->id;
            $sql = 'SELECT image 
                    FROM ' . _DB_PREFIX_ . 's2i_slides_lang 
                    WHERE id_slide = ' . (int)$slide['id_slide'] . ' 
                    AND id_lang = ' . (int)$id_lang;
            $slide['image'] = Db::getInstance()->getValue($sql);
            return $slide;
        }, $slides);

        $this->context->smarty->assign([
            'editForm' => $editForm,
            'slidesList' => $slidesList,
            'id_section' => $id_section,
            'slides' => $slides,

        ]);

        return $this->module->display($this->module->getLocalPath(), 'views/templates/admin/panel_section_slide.tpl');
    }

    protected function renderAddSlideForm()
    {
        $id_section = (int)Tools::getValue('id_section');

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
        $db = Db::getInstance();
        // Création de la section principale
        $section = new Section();
        $section->name = Tools::getValue('name');
        $section->active = (int)Tools::getValue('active');
        $section->is_slider = (int)Tools::getValue('is_slider');
        $section->speed = (int)Tools::getValue('speed', 5000);
        $section->position = (int)Tools::getValue('position', 0);

        // Vérif si le existe ou si il est unique
        $sectionName = Tools::getValue('name');
        if (empty($sectionName)) {
            $this->errors[] = $this->trans('Le nom de la section est requis');
            return false;
        }

        $existingSection = Db::getInstance()->getValue(
            'SELECT id_section FROM `' . _DB_PREFIX_ . 's2i_sections` WHERE name = "' . pSQL($sectionName) . '"'
        );

        if ($existingSection) {
            $this->errors[] = $this->trans('Une section avec ce nom existe déjà.');
            return false;
        }

        if (!$section->add()) {
            $this->errors[] = $this->trans('Erreur lors de la création de la section');
            return false;
        }

        // Insertion des hooks sélectionnés multiples
        $selectedHooks = Tools::getValue('hook_location');
        if (is_array($selectedHooks)) {
            foreach ($selectedHooks as $hookName) {
                $result = $db->insert('s2i_section_hooks', [
                    'id_section' => (int)$section->id,
                    'hook_name' => pSQL($hookName)
                ]);

                if (!$result) {
                    $this->errors[] = $this->trans('Erreur lors de l\'association du hook: ') . $hookName;
                    return false;
                }
            }
        }

        $this->context->cookie->__set('s2i_success_message', 'Section créée avec succès');
        $this->context->cookie->write();

        return true;
    }

    protected function handleDeleteSlide()
    {
        $id_slide = (int)Tools::getValue('id_slide');

        // Vérifier si le slide existe
        $slide = new Slide($id_slide);
        if (!Validate::isLoadedObject($slide)) {
            $this->errors[] = $this->trans('Slide introuvable');
            return false;
        }

        // Supprimer d'abord les images associées
        $slideLangs = Db::getInstance()->executeS(
            '
        SELECT image, image_mobile 
        FROM `' . _DB_PREFIX_ . 's2i_slides_lang` 
        WHERE id_slide = ' . (int)$id_slide
        );

        foreach ($slideLangs as $slideLang) {
            // Supprimer l'image principale
            if (!empty($slideLang['image'])) {
                $imagePath = _PS_IMG_DIR_ . $slideLang['image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            // Supprimer l'image mobile
            if (!empty($slideLang['image_mobile'])) {
                $imageMobilePath = _PS_IMG_DIR_ . $slideLang['image_mobile'];
                if (file_exists($imageMobilePath)) {
                    unlink($imageMobilePath);
                }
            }
        }

        // Supprimer les traductions
        Db::getInstance()->execute(
            '
        DELETE FROM `' . _DB_PREFIX_ . 's2i_slides_lang` 
        WHERE id_slide = ' . (int)$id_slide
        );

        // Supprimer le slide
        if (!$slide->delete()) {
            $this->errors[] = $this->trans('Erreur lors de la suppression du slide');
            return false;
        }

        // Reorganise les positions
        Db::getInstance()->execute(
            '
        UPDATE `' . _DB_PREFIX_ . 's2i_section_slides` ss
        JOIN (
        SELECT id_slide, @row_number:=@row_number+1 AS new_position
        FROM `' . _DB_PREFIX_ . 's2i_section_slides`, 
        (SELECT @row_number:=0) AS t
        WHERE id_section = ' . (int)$slide->id_section . '
        ORDER BY position ASC) AS ranked
        ON ss.id_slide = ranked.id_slide
        SET ss.position = ranked.new_position
        WHERE ss.id_section = ' . (int)$slide->id_section
        );

        $this->context->cookie->__set('s2i_success_message', 'Slide supprimé avec succès');
        $this->context->cookie->write();

        return true;
    }
    protected function handleUpdateSectionOnly()
    {
        $db = Db::getInstance();
        $id_section = (int)Tools::getValue('id_section');
        $section = new Section($id_section);

        // Mise à jour des informations de base de la section
        $section->name = Tools::getValue('name');
        $section->active = (int)Tools::getValue('active');
        $section->is_slider = (int)Tools::getValue('is_slider');
        $section->speed = (int)Tools::getValue('speed');
        $section->position = (int)Tools::getValue('position', 0);

        if (!$section->update()) {
            $this->errors[] = $this->trans('Erreur lors de la mise à jour de la section');
            return false;
        }

        // Gestion des hooks
        // 1. Supprimer les anciens hooks
        $db->delete('s2i_section_hooks', 'id_section = ' . (int)$id_section);

        // 2. Ajouter les nouveaux hooks sélectionnés
        $selectedHooks = Tools::getValue('hook_location');
        if (is_array($selectedHooks)) {
            foreach ($selectedHooks as $hookName) {
                $result = $db->insert('s2i_section_hooks', [
                    'id_section' => (int)$id_section,
                    'hook_name' => pSQL($hookName)
                ]);

                if (!$result) {
                    $this->errors[] = $this->trans('Erreur lors de la mise à jour du hook: ') . $hookName;
                    return false;
                }
            }
        }

        $this->context->cookie->__set('s2i_success_message', 'Section mise à jour avec succès');
        $this->context->cookie->write();
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminS2iGestionlist') . '&id_section=' . $id_section);
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
        // Récupérer le nombre actuel de slides pour la position
        $currentSlidesCount = Db::getInstance()->getValue(
            'SELECT COUNT(*) 
            FROM `' . _DB_PREFIX_ . 's2i_section_slides` 
            WHERE id_section = ' . (int)$id_section
        );

        $slide = new Slide();
        $slide->id_section = $id_section;
        $slide->active = (int)Tools::getValue('active');
        $slide->only_title = (int)Tools::getValue('only_title');
        $slide->title_hide = (int)Tools::getValue('title_hide', 0);
        $slide->image_is_mobile = (int)Tools::getValue('image_mobile_enabled');
        $slide->position = $currentSlidesCount + 1;
        $slide->display_datePicker = (int)Tools::getValue('display_datePicker');
        if (!$slide->add()) {
            $this->errors[] = $this->trans('Erreur lors de la création du slide');
            return false;
        }
        if ($slide->display_datePicker) {
            $slide->start_date = Tools::getValue('start_date');
            $slide->end_date = Tools::getValue('end_date');
        } else {
            $slide->start_date = null;
            $slide->end_date = null;
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
        $uploadDir = _PS_IMG_DIR_ . 's2i_gestionlist/';

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
            return 's2i_gestionlist/' . $imageName;
        }

        $this->errors[] = $this->trans('Erreur lors du téléchargement de l\'image pour la langue ') . $id_lang;
        return false;
    }
}