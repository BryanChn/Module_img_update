<div class="bootstrap">
    {* Section Paramètres *}
    <div class="panel">
        <div class="panel-body">
            {$editForm}
        </div>
    </div>

    {* Section Liste des diapositives *}
    <div class="panel">
        <a href="{$link->getAdminLink('AdminS2iImage', true, [], [
        'id_section' => $id_section,
        'edits2i_section_slides' => 1,
        'id_slide' => 0
    ])|escape:'html':'UTF-8'}" class="btn btn-primary">
            {l s='Ajouter un slide' mod='s2i_update_img'}
        </a>
        <div class="panel-body">
            {$slidesList}
        </div>
    </div>
</div>