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
            $this->handleDeleteList();
        } elseif ($action === 'modify' && Tools::getValue('id')) {
            $this->handleModifyList();
        } elseif ($action === 'create') {
            $this->handleCreateList();
        }
        parent::postProcess();
    }

    private function handleDeleteList()
    {
        $id = (int)Tools::getValue('id');
        $list = new S2iLists($id);

        if (Validate::isLoadedObject($list)) {
            if ($list->delete()) {
                $this->confirmations[] = $this->trans('La liste a été supprimée avec succès.', [], 'Modules.S2iUpdateImg.Admin');
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->module->name . '&conf=1');
            } else {
                $this->errors[] = $this->trans('Erreur : Impossible de supprimer cette liste.', [], 'Modules.S2iUpdateImg.Admin');
            }
        } else {
            $this->errors[] = $this->trans('Erreur : Objet introuvable pour la suppression.', [], 'Modules.S2iUpdateImg.Admin');
        }
    }

    private function handleModifyList()
    { {
            $id = (int)Tools::getValue('id');
            $list = new S2iLists($id);

            if (Validate::isLoadedObject($list)) {

                if (Tools::isSubmit('submitModify')) {

                    $list->name = Tools::getValue('name');
                    $list->active = (int)Tools::getValue('active');
                    $list->slider = (int)Tools::getValue('slider');
                    $list->speed = (int)Tools::getValue('speed');

                    if ($list->update()) {
                        $this->confirmations[] = $this->trans('La liste a été modifiée avec succès.', [], 'Modules.S2iUpdateImg.Admin');
                        Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->module->name . '&conf=4');
                    } else {
                        $this->errors[] = $this->trans('Erreur : Impossible de modifier cette liste.', [], 'Modules.S2iUpdateImg.Admin');
                    }
                } else {

                    $this->context->smarty->assign([
                        'list' => $list,
                        'hooks' => ['home', 'footer', 'header'],
                        'advanced_options_link' => $this->context->link->getAdminLink('AdminS2iImage') . '&action=advanced_options&id=' . $id,
                    ]);

                    // Charger le template de modification
                    $this->setTemplate('modify.tpl');
                }
            } else {
                $this->errors[] = $this->trans('Erreur : Objet introuvable pour la modification.', [], 'Modules.S2iUpdateImg.Admin');
            }
        }
    }
    private function handleCreateList()

    {
        $name = Tools::getValue('name');
        $active = Tools::getValue('active') ? 1 : 0;
        $slider = Tools::getValue('slider') ? 1 : 0;
        $speed = Tools::getValue('speed') ? (int)Tools::getValue('speed') : 5000;
        $imagePath = null;

        if ($name) {
            $list = new S2iLists();
            $list->name = $name;
            $list->active = $active;
            $list->slider = $slider;
            $list->speed = $slider ? $speed : null;

            // Normalisation du nom pour le fichier
            $safeName = Tools::str2url($name);

            // Vérifie si "Image pour mobile" est cochée via `is_mobile_image dans le configuration.tpl`
            $isMobileImage = Tools::getValue('is_mobile_image') ? '-m-' : '-';

            // Gestion de l'upload de l'image principale
            if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
                // Ajoute le suffixe "-m-" si nécessaire
                $imageName = $safeName . $isMobileImage . uniqid() . '.jpg';
                $uploadDir = _PS_IMG_DIR_ . 's2i_update_img/';

                // Création du dossier si inexistant
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $uploadPath = $uploadDir . $imageName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    $imagePath = 's2i_update_img/' . $imageName;
                    $list->image = $imagePath;
                } else {
                    $this->errors[] = $this->trans('Erreur lors du téléchargement de l\'image.');
                }
            }


            if ($list->add()) {
                $this->confirmations[] = $this->trans('La nouvelle liste a été créée avec succès.');
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->module->name . '&conf=1');
            } else {
                $this->errors[] = $this->trans('Erreur : Impossible de créer la liste.');
            }
        } else {
            $this->errors[] = $this->trans('Erreur : Le nom de la liste est requis.');
        }
    }
}
