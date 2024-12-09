<div class="bootstrap">
    <style>
        .drag-handle {
            cursor: move;
            cursor: -webkit-grab;
            cursor: grab;
        }

        .ui-sortable-helper .drag-handle {
            cursor: -webkit-grabbing;
            cursor: grabbing;
        }

        .drag-indicator {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px;
            border-radius: 4px;
            background: #fafbfc;
            border: 1px solid #eee;
            transition: all 0.2s ease;
        }

        .drag-indicator:hover {
            background: #f6f8fa;
            border-color: #ddd;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .drag-indicator i.icon-move {
            color: #6c868e;
            font-size: 16px;
            transition: color 0.2s ease;
        }

        .drag-indicator:hover i.icon-move {
            color: #25b9d7;
        }

        .position-number {
            background: #25b9d7;
            color: white;
            min-width: 24px;
            height: 24px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 13px;
            padding: 0 8px;
            transition: all 0.2s ease;
        }

        .drag-indicator:hover .position-number {
            background: #21a6c1;
        }

        /* Style pour le tableau */
        .table-bordered {
            border-color: #ddd;
        }

        .table-striped>tbody>tr:nth-of-type(odd) {
            background-color: #fafbfc;
        }
    </style>
    {* Section Paramètres *}
    <div class="panel">
        <h3>
            <i class="icon-edit"></i> {l s='Édition de la section' mod='s2i_gestionlist'}
            <a href="{$link->getAdminLink('AdminModules', true)}&configure=s2i_gestionlist"
                class="btn btn-default pull-right">
                <i class="icon-arrow-left"></i> {l s='Retour' mod='s2i_gestionlist'}
            </a>
        </h3>
        <div class="panel-body">
            {$editForm}
        </div>
    </div>

    {* Section Liste des diapositives *}
    <div class="panel">
        <h3>
            <i class="icon-picture"></i> {l s='Slides de la section' mod='s2i_gestionlist'}
            <a href="{$link->getAdminLink('AdminS2iGestionlist')}&id_section={$id_section}&add_slide=1"
                class="btn btn-primary pull-right">
                {l s='Ajouter un slide' mod='s2i_gestionlist'}
            </a>
        </h3>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="slides-table">
                    <thead>
                        <tr>
                            <th class="fixed-width-xs center">Position</th>
                            <th class="center">ID</th>
                            <th class="left">Titre</th>
                            <th class="center">Actif</th>
                            <th class="center">Image</th>
                            <th class="center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="slides-list">
                        {foreach from=$slides item=slide}
                            <tr data-id="{$slide.id_slide}" data-position="{$slide.position}">
                                <td class="drag-handle center">
                                    <div class="drag-indicator">
                                        <i class="icon-move"></i>
                                        <div class="position-indicator">{$slide.position}</div>
                                    </div>
                                </td>
                                <td class="center">{$slide.id_slide}</td>
                                <td class="left">{$slide.title}</td>
                                <td class="center">
                                    <span class="badge {if $slide.active}badge-success{else}badge-danger{/if}">
                                        {if $slide.active}Actif{else}Inactif{/if}
                                    </span>
                                </td>


                                <td class="center">
                                    {if isset($slide.image) && $slide.image}
                                        <img src="{$link->getBaseLink()}{$img_dir}{$slide.image}" alt="" class="img-thumbnail"
                                            style="max-height: 90px;">
                                    {else}
                                        <p class="text-muted">Aucune image disponible</p>
                                    {/if}
                                </td>

                                <td class="center">
                                    <div class="btn-group">
                                        <a href="{$link->getAdminLink('AdminS2iGestionlist')}&id_section={$id_section}&id_slide={$slide.id_slide}&edits2i_section_slides"
                                            class="btn btn-default">
                                            <i class="icon-edit"></i>
                                        </a>
                                        <a href="{$link->getAdminLink('AdminS2iGestionlist')}&id_section={$id_section}&id_slide={$slide.id_slide}&deletes2i_section_slides"
                                            class="btn btn-default delete-slide"
                                            onclick="return confirm('{l s='Attention ! Cette action supprimera définitivement le slide et ses images. Êtes-vous sûr de vouloir continuer ?' mod='s2i_gestionList'}');">
                                            <i class="icon-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.slides-list').sortable({
            handle: '.drag-handle',
            axis: 'y',
            update: function(event, ui) {
                var slidesOrder = [];
                $('.slides-list tr').each(function(index) {
                    slidesOrder.push({
                        id: $(this).data('id'),
                        position: index
                    });
                });

                $.ajax({
                    url: s2iGestionListConfig.updateUrl,
                    method: 'POST',
                    data: {
                        ajax: 1,
                        action: 'updatePositions',
                        positions: JSON.stringify(slidesOrder),
                        token: s2iGestionListConfig.token
                    },
                    success: function(response) {
                        var parsedResponse = typeof response === 'string' ? JSON.parse(
                            response) : response;
                        if (parsedResponse.success) {
                            showSuccessMessage(s2iGestionListConfig.successMessage);
                            // Mettre à jour les numéros immédiatement
                            $('.slides-list tr').each(function(index) {
                                $(this).find('.position-number').text(index +
                                    1);
                                $(this).data('position', index);
                            });
                        } else {
                            showErrorMessage(parsedResponse.message ||
                                s2iGestionListConfig.errorMessage);
                            // Annuler le tri si erreur
                            $('.slides-list').sortable('cancel');
                        }
                    },
                    error: function() {
                        showErrorMessage(s2iGestionListConfig.errorMessage);
                        $('.slides-list').sortable('cancel');
                    }
                });
            }
        });
    });
</script>