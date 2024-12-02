<div class="search-menu-wrapper">
    {if isset($slides) && $slides|count > 0}
        <div class="search-slides">
            <ul class="search-slides-list">
                {foreach from=$slides item=slide}
                    <li class="search-slide-item">
                        {if $slide.url}
                            <button class="button-80" role="button">
                                <a href="{$slide.url|escape:'html':'UTF-8'}">
                                    {$slide.title|escape:'html':'UTF-8'}
                                </a>
                            </button>
                        {else}
                            {$slide.title|escape:'html':'UTF-8'}
                        {/if}
                    </li>
                {/foreach}
            </ul>
        </div>
    {/if}
</div>



<style>
    .search-slides-list {
        display: flex;
        flex-direction: row;
        gap: 10px;
    }

    .button-80 {
        background: #fff;
        backface-visibility: hidden;
        border-radius: .375rem;
        border-style: solid;
        border-width: .125rem;
        box-sizing: border-box;
        color: #212121;
        cursor: pointer;
        display: inline-block;
        font-family: Circular, Helvetica, sans-serif;
        font-size: 1rem;
        font-weight: 700;
        letter-spacing: -.01em;
        line-height: 1.3;
        padding: .875rem 1.125rem;
        position: relative;
        text-align: left;
        text-decoration: none;
        transform: translateZ(0) scale(1);
        transition: transform .2s;
        user-select: none;
        -webkit-user-select: none;
        touch-action: manipulation;
    }

    .button-80:not(:disabled):hover {
        transform: scale(1.05);
    }

    .button-80:not(:disabled):hover:active {
        transform: scale(1.05) translateY(.125rem);
    }

    .button-80:focus {
        outline: 0 solid transparent;
    }

    .button-80:focus:before {
        content: "";
        left: calc(-1*.375rem);
        pointer-events: none;
        position: absolute;
        top: calc(-1*.375rem);
        transition: border-radius;
        user-select: none;
    }

    .button-80:focus:not(:focus-visible) {
        outline: 0 solid transparent;
    }

    .button-80:focus:not(:focus-visible):before {
        border-width: 0;
    }

    .button-80:not(:disabled):active {
        transform: translateY(.125rem);
    }
</style>