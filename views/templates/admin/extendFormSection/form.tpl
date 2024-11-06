{extends file="helpers/form/form.tpl"}
{block name="field"}
    {if $input.type == 'file_lang'}
        <div class="row{if isset($input.mobile) && $input.mobile} mobile-image{/if}"
            style="{if isset($input.mobile) && $input.mobile}display:none;{/if}">
            {foreach from=$languages item=language}
                <div class="translatable-field lang-{$language.id_lang}"
                    {if $language.id_lang != $defaultFormLanguage}style="display:none" {/if}>
                    <div class="col-lg-9">
                        {if isset($fields[0]['form'][$input.group_name])}
                            <img src="{$image_baseurl}{$fields[0]['form'][$input.group_name][$language.id_lang]}" class="img-thumbnail"
                                width="50px" />
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
                        <div class="col-lg-3">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                {$language.iso_code}
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach from=$languages item=lang}
                                    <li><a href="javascript:hideOtherLanguage({$lang.id_lang});" tabindex="-1">{$lang.name}</a></li>
                                {/foreach}
                            </ul>
                        </div>
                    {/if}
                </div>
                <script>
                    $(document).ready(function() {
                        $('#{$input.name}_{$language.id_lang}-selectbutton').click(function(e){
                        $('#{$input.name}_{$language.id_lang}').trigger('click');
                    });
                    $('#{$input.name}_{$language.id_lang}').change(function(e){
                    var val = $(this).val();
                    var file = val.split(/[\\/]/);
                    $('#{$input.name}_{$language.id_lang}-name').val(file[file.length-1]);
                    });
                    });
                </script>
            {/foreach}
        </div>
    {/if}
    {$smarty.block.parent}
{/block}

{block name="after"}
    <script type="text/javascript">
        $(document).ready(function() {
            // Gestion de l'affichage des champs en fonction de only_title
        function toggleOnlyTitleFields() {
            var onlyTitle = $('#only_title_on').prop('checked');
            if (onlyTitle) {
                // Cache tous les champs optionnels
                $('.legend-url-group').hide(); // Utilise la nouvelle classe
                $('div.row').closest('.form-group').hide();
                $('[name="image_is_mobile"]').closest('.form-group').hide();
            } else {
                // Réaffiche tous les champs
                $('.legend-url-group').show(); // Utilise la nouvelle classe
                $('div.row').closest('.form-group').show();
                $('[name="image_is_mobile"]').closest('.form-group').show();

            }
        }

        // Gestion de l'affichage de l'upload mobile
        function toggleMobileImageUpload() {
            var isMobile = $('#image_is_mobile_on').prop('checked');
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
        });
    </script>
{/block}