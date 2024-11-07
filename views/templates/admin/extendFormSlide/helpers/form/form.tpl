{extends file="helpers/form/form.tpl"}
{block name="input"}

    {if $input.type == 'file_lang'}


        <div class="row{if isset($input.mobile) && $input.mobile} mobile-image{/if}"
            style="{if isset($input.mobile) && $input.mobile}display:none;{/if}">

            {foreach from=$languages item=language}
                {assign var=lang_id value=$language.id_lang}
                <div class="translatable-field lang-{$lang_id}" {if $lang_id != $defaultFormLanguage}style="display:none" {/if}>
                    <div class="col-lg-9">
                        {* Affichage de l'image existante *}

                        <img src="{$fields_value["image{$lang_id}" ]}" class="img-thumbnail" width="50px" />

                        {* Champs texte *}
                        {if $input.name == 'title' || {$input.name} == 'legend' || $input.name == 'url'}
                            <input type="text" name="{$input.name}_{$lang_id}" value="{$fields_value["{$input.name}_{$lang_id}"
                ]|escape:'html':'UTF-8'}" class="form-control" />
                        {else}
                            {* Upload d'image *}
                            <div class="dummyfile input-group">
                                <input id="{$input.name}_{$lang_id}" type="file" name="{$input.name}_{$lang_id}"
                                    class="hide-file-upload" />
                                <span class="input-group-addon"><i class="icon-file"></i></span>
                                <input id="{$input.name}_{$lang_id}-name" type="text" class="disabled" name="filename" readonly />
                                <span class="input-group-btn">
                                    <button id="{$input.name}_{$lang_id}-selectbutton" type="button" name="submitAddAttachments"
                                        class="btn btn-default">
                                        <i class="icon-folder-open"></i> {l s='Choisir une image' d='Admin.Actions'}
                                    </button>
                                </span>
                            </div>
                        {/if}
                    </div>

                    {* Sélecteur de langue *}
                    {if $languages|count > 1}
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                {$language.iso_code}
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach from=$languages item=lang}
                                    <li>
                                        <a href="javascript:hideOtherLanguage({$lang.id_lang});">{$lang.name}</a>
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
                    $('.legend-url-group').closest('.form-group').hide();
                    $('.file_lang').closest('.form-group').hide();
                    $('input[name="image_is_mobile"]').closest('.form-group').hide();
                    $('.mobile-image').hide().closest('.form-group').hide();
                    $('.img-current').hide().closest('.form-group').hide();
                } else {
                    $('.legend-url-group').closest('.form-group').show();
                    $('.file_lang').closest('.form-group').show();
                    $('input[name="image_is_mobile"]').closest('.form-group').show();
                    $('.mobile-image').show().closest('.form-group').show();
                    $('.img-current').show().closest('.form-group').show();
                }
            }

            function toggleMobileImageUpload() {
                var isMobile = $('input[name="image_is_mobile"]:checked').val() == 1;
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