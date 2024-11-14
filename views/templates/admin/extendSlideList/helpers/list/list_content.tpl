{extends file="helpers/list/list_content.tpl"}

{block name="td_content"}
    {if $key == 'position'}
        <div class="dragHandle" style="cursor: move;">
            <i class="material-icons">drag_indicator</i>
            <span class="position">{if isset($tr.position)}{$tr.position|intval}{else}0{/if}</span>

        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{block name="tr_attrs"}
    data-id="{$tr.id_slide}"
{/block}

{block name="list_footer"}
    {$smarty.block.parent}
    <script type="text/javascript">
        console.log('Début du script de tri');

        function initSortable() {
            console.log('Tentative d\'initialisation du tri');
            var $table = $('.table');
            console.log('Table trouvée:', $table.length);

            if ($table.length) {
                $table.find('tbody').sortable({
                    handle: '.dragHandle',
                    helper: function(e, ui) {
                        ui.children().each(function() {
                            $(this).width($(this).width());
                        });
                        return ui;
                    },
                    update: function(event, ui) {
                        var positions = [];

                        // Construction du tableau des positions
                        $(this).find('tr').each(function(index) {
                            positions.push({
                                id_slide: $(this).attr(
                                    'data-id'), // Utilisation de attr au lieu de data
                                position: index + 1
                            });
                            $(this).find('.position').text(index + 1);
                        });

                        console.log('Positions à envoyer:', positions);

                        // Envoi AJAX
                        $.ajax({
                            type: 'POST',
                            url: '{$currentIndex|escape:'javascript':'UTF-8'}&updateSlidesPosition=1&token={$token|escape:'javascript':'UTF-8'}',
                            data: {
                                positions: JSON.stringify(positions)
                            },
                            success: function(response) {
                                console.log('Réponse reçue:', response);
                                try {
                                    var data = JSON.parse(response);
                                    if (data.success) {
                                        showSuccessMessage('{l s='Positions mises à jour avec succès' d='Admin.Notifications.Success'}');
                                    } else {
                                        showErrorMessage(data.error || '{l s='Erreur lors de la mise à jour des positions' d='Admin.Notifications.Error'}');
                                    }
                                } catch (e) {
                                    console.error('Erreur parsing JSON:', e);
                                    showErrorMessage('{l s='Erreur lors de la mise à jour des positions' d='Admin.Notifications.Error'}');
                                }
                            },

                        });
                    }
                });
                console.log('Tri initialisé avec succès');
            }
        }

        // Attendre que le document soit prêt
        $(document).ready(function() {
            console.log('Document prêt');
            initSortable();
        });
    </script>
{/block}