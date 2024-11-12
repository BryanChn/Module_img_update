<div class="bootstrap">
    {* Section Paramètres *}
    <div class="panel">
        <h3>
            <i class="icon-edit"></i> {l s='Édition de la section' mod='s2i_update_img'}
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
            <i class="icon-picture"></i> {l s='Slides de la section' mod='s2i_update_img'}
            <a href="{$link->getAdminLink('AdminS2iImage')}&id_section={$id_section}&add_slide=1"
                class="btn btn-primary pull-right">
                {l s='Ajouter un slide' mod='s2i_update_img'}
            </a>
        </h3>
        <div class="panel-body">

            {$slidesList}
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var $slidesList = $("#slides-list tbody");

        if ($slidesList.length) {
            $slidesList.sortable({
                opacity: 0.6,
                cursor: "move",
                axis: 'y',
                update: function(event, ui) {
                    var order = [];
                    $slidesList.find('tr').each(function(index) {
                        order.push($(this).data('id'));
                    });

                    $.ajax({
                        type: 'POST',
                        url: '{$link->getAdminLink('AdminS2iImage')}&ajax=1&action=updateSlidesPosition',
                        data: { order: order },
                        success: function(response) {
                            var result = JSON.parse(response);
                            if (result.error) {
                                showErrorMessage(
                                    'Erreur lors de la mise à jour des positions');
                            }
                        }
                    });
                }
            });
        }
    });
</script>