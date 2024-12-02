{foreach from=$slides item=slide}
    {if $slide.url}
        <a href="{$slide.url|escape:'html':'UTF-8'}">
            {$slide.title|escape:'html':'UTF-8'}
        </a>
    {/if}
{/foreach}