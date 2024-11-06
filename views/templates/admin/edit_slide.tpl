<div class="bootstrap">
    <div class="panel">
        <h3>
            <i class="icon-edit"></i> {l s='Édition du slide' mod='s2i_update_img'}
            <a href="{$link->getAdminLink('AdminS2iImage')}&id_section={$id_section}"
                class="btn btn-default pull-right">
                <i class="icon-arrow-left"></i> {l s='Retour' mod='s2i_update_img'}
            </a>
        </h3>
        <div class="panel-body">
            {$editForm}
        </div>
    </div>
</div>