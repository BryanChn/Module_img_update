{extends file="helpers/form/form.tpl"}

{block name="field"}
    {if $input.type == 'file_lang'}
        <div class="row{if isset($input.form_group_class)} {$input.form_group_class}{/if}">
            {foreach from=$languages item=language}
                <div class="translatable-field lang-{$language.id_lang}"
                    {if $language.id_lang != $defaultFormLanguage}style="display:none" {/if}>
                    <div class="col-lg-5">
                        {if isset($fields[0]['form'][$input.group_name])}
                            <img src="{$image_baseurl}{$fields[0]['form'][$input.group_name][$language.id_lang]}" class="img-thumbnail"
                                width="250px" />
                        {/if}
                        <div class="dummyfile input-group">
                            <input id="{$input.name}_{$language.id_lang}" type="file" name="{$input.name}_{$language.id_lang}"
                                class="hide-file-upload" />
                            <span class="input-group-addon"><i class="icon-file"></i></span>
                            <input id="{$input.name}_{$language.id_lang}-name" type="text" class="disabled" name="filename"
                                readonly />
                            <span class="input-group-btn">
                                <button id="{$input.name}_{$language.id_lang}-selectbutton" type="button"
                                    name="submitAddAttachments" class="btn btn-default">
                                    <i class="icon-folder-open"></i> {l s='Choisir une image' d='Admin.Actions'}
                                </button>
                            </span>
                        </div>
                    </div>
                    {if $languages|count > 1}
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                {$language.iso_code}
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach from=$languages item=lang}
                                    <li>
                                        <a href="javascript:hideOtherLanguage({$lang.id_lang});" tabindex="-1">{$lang.name}</a>
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                    {/if}
                </div>
            {/foreach}
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{block name="after"}
    <script type="text/javascript">
        $(document).ready(function() {


            function toggleOnlyTitleFields() {
                var onlyTitle = $('input[name="only_title"]:checked').val() == 1;
                if (onlyTitle) {

                    $('input[name="image_is_mobile"]').closest('.form-group').hide();
                    $('.mobile-image').hide().closest('.form-group').hide();
                    $('.image').hide().closest('.form-group').hide();
                } else {

                    $('input[name="image_is_mobile"]').closest('.form-group').show();
                    $('.mobile-image').show().closest('.form-group').show();
                    $('.image').show().closest('.form-group').show();
                }
            }

            // Gestion de l'affichage de l'upload mobile
            function toggleMobileImageUpload() {
                var isMobile = $('input[name="image_is_mobile"]:checked').val() == 1 &&
                    $('input[name="only_title"]:checked').val() == 0;
                if (isMobile) {
                    $('.mobile-image').show();
                } else {
                    $('.mobile-image').hide();
                }
            }

            // Initialisation
            toggleOnlyTitleFields();
            toggleMobileImageUpload();


            // Événements
            $('input[name="only_title"]').change(toggleOnlyTitleFields);
            $('input[name="image_is_mobile"]').change(toggleMobileImageUpload);

            // Gestion des boutons de sélection de fichier
            $('button[id$="-selectbutton"]').on('click', function() {
                var inputId = $(this).attr('id').replace('-selectbutton', '');
                $('#' + inputId).click();
            });

            // Mise à jour du nom du fichier sélectionné
            $('input[type="file"].hide-file-upload').change(function() {
                var id = $(this).attr('id');
                var fileName = $(this).val().split('\\').pop();
                $('#' + id + '-name').val(fileName);
            });
        });
    </script>
{/block}