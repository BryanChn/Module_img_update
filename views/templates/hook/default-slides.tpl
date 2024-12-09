<div class="s2i-update-img">

    {foreach from=$slides item=slide}
        {if $slide.url}
            <a href="{$slide.url|escape:'html':'UTF-8'}">
                {$slide.title|escape:'html':'UTF-8'}
            </a>
        {else}
            {$slide.title|escape:'html':'UTF-8'}
        {/if}
    {/foreach}
</div>