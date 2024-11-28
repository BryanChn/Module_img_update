<div class="bootstrap">
    <div class="panel">
        <input type="hidden" name="id_section" value="{$id_section}" />
        <input type="hidden" name="id_slide" value="{$id_slide}" />
        {$editForm}

        <div class="panel-footer">

            <a href="{$link->getAdminLink('AdminS2iImage')}&id_section={$id_slide}&id_section={$id_section}"
                class="btn btn-default pull-right">
                <i class="icon-arrow-left"></i> {l s='Retour' mod='s2i_update_img'}
            </a>
        </div>
    </div>
</div>