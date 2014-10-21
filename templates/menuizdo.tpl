
{foreach from=$menu item=i key=k name=menu}
    <li>
        <a href="{$baseurl}/{$k}"><i class="fa fa-{$i.icon} fa-fw"></i> {$i.title}{if isset($i.menu)}<span class="fa arrow"></span>{/if}</a>
        {if isset($i.menu)}
        <ul class="nav nav-second-level collapse {if $menu_active == $k}in{/if}">
        {foreach from=$i.menu item=j key=l name=submenu}
            <li>
                <a href="{$baseurl}/{$l}"><i class="fa fa-{$j.icon} fa-fw"></i> {$j.title}</a>
            </li>
        {/foreach}
        </ul>
        {/if}
    </li>
{/foreach}

