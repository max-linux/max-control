

<!-- menu izdo -->

{foreach from=$mainmenu key=key item=menu}
<li style='display:inline;' > 
    <a title="{$menu}" href="{$basedir}/{$key}" class="navc" target="_parent">{$menu}</a> 
    {if $submenu }
    {if $key == $module}
    <ul class='submenu2'> 
        {foreach from=$submenu key=subkey item=submenu}
            <li class='menuUsersAndGroups2'> 
                <a title="{$submenu}" href="{$basedir}/{$key}/{$subkey}" class="navc" target="_parent">{$submenu}</a> 
            </li>
        {/foreach}
    </ul> 
    {/if}
    {/if}
</li>
{/foreach}

<!-- fin menu izdo -->
