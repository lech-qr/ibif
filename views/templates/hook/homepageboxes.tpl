<div class="homepage-boxes-container container">
    <h1>{l s='Recruitment task for IBIF'}</h1>
    <div class="row">
    {foreach from=$boxes item=box}
    <div class="col-md-4 col-sm-12">
        <a href="{$box.link}" class="homepage-box shadow bg-body rounded" style="background-image: url('/ibif/{$box.background_image}');">
            <h2>{$box.title}</h2>
        </a>    
    </div>
    {/foreach}    
    </div>
</div>
