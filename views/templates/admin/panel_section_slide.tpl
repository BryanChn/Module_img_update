<div class="bootstrap">
    {* Section Paramètres *}
    <div class="panel">
        <h3><i class="icon-cogs"></i> {l s='Paramètres de la section' mod='s2i_update_img'}</h3>
        <div class="panel-body">
            {$editForm}
        </div>
    </div>

    {* Section Liste des diapositives *}
    <div class="panel">
        <h3>
            <i class="icon-list"></i> {l s='Liste des images' mod='s2i_update_img'}
            <span class="panel-heading-action">
                <a class="btn btn-default" href="#" id="add_new_slide">
                    <i class="icon-plus"></i> {l s='' mod='s2i_update_img'}
                </a>
            </span>
        </h3>
        <div class="panel-body">

            {$slidesList}

        </div>
    </div>
</div>