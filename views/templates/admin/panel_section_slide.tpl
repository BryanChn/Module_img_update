<div class="bootstrap">
    {* Section Paramètres *}
    <div class="panel">
        <h3>
            <i class="icon-edit"></i> {l s='Édition du slide' mod='s2i_update_img'}
            <a href="{$link->getAdminLink('AdminModules', true)}&configure=s2i_update_img"
                class="btn btn-default pull-right">
                <i class="icon-arrow-left"></i> {l s='Retour' mod='s2i_update_img'}
            </a>
        </h3>
        <div class="panel-body">
            {$editForm}
        </div>
    </div>

    {* Section Liste des diapositives *}
    <div class="panel">
        <h3>
            <i class="icon-picture"></i> {l s='Slides' mod='s2i_update_img'}
            <a href="{$link->getAdminLink('AdminS2iImage')}&id_section={$id_section}&add_slide=1"
                class="btn btn-primary pull-right">
                <i class="icon-plus"></i> {l s='' mod='s2i_update_img'}
            </a>
        </h3>
        <div class="panel-body">
            {$slidesList}
        </div>
    </div>
</div>