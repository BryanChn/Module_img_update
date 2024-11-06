{extends file="helpers/form/form.tpl"}

{block name="field"}
    {if $input.type == 'file_lang'}
        <div class="row{if isset($input.mobile) && $input.mobile} mobile-image{/if}"
            style="{if isset($input.mobile) && $input.mobile}display:none;{/if}">
            {foreach from=$languages item=language}
                <div class="translatable-field lang-{$language.id_lang}"
                    {if $language.id_lang != $defaultFormLanguage}style="display:none" {/if}>
                    <div class="col-lg-9">
                        {if isset($fields_value[$input.name][$language.id_lang]) && $fields_value[$input.name][$language.id_lang] != ''}
                            <img src="{$image_baseurl}{$fields_value[$input.name][$language.id_lang]}" class="img-thumbnail"
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
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1">
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
                } else {
                    $('.legend-url-group').closest('.form-group').show();
                    $('.file_lang').closest('.form-group').show();
                    $('input[name="image_is_mobile"]').closest('.form-group').show();
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