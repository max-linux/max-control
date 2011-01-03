
<h2>Apagado o reinicio de equipos ({$pager->getMAX()})</h2>



<table class="bDataTable"> 
    <tr> 
        <td> 
        <form action="{$urlform}" method="post"> 
          <input type='text' name='Filter' id='Filter' value="{$filter}" /> 
          <input type='submit' name='button' value="Buscar" title="Buscar" /> 
        </form>
        </td> 
    </tr> 
</table> 


<table class='dataTable'> 
    <thead> 
    <tr>
      <th class=''>Nombre {$pager->getSortIcons('uid')}</th> 
      <th class=''>IP {$pager->getSortIcons('ipHostNumber')} / MAC {$pager->getSortIcons('macAddress')}</th> 
      <th class=''>Aula {$pager->getSortIcons('sambaProfilePath')}</th> 
      <th class=''>Acciones</th> 
      <th class=''>Encender/Reiniciar en</th> 
    </tr>
    </thead>
 
 
    <tbody> 
      {foreach from=$equipos item=u}
      {if $u->teacher_in_computer()}
      <tr class='border' id="{$u->hostname()}"> 
        <td class='tcenter'><span>{$u->attr('uid')}</span></td> 
        <td class='tcenter'><span>{$u->attr('ipHostNumber')} / {$u->attr('macAddress')}</span></td> 
        <td class='tcenter'><span>{$u->attr('sambaProfilePath')}</span></td>
        <td class='tcenter'> 
            <a href="{$urlpoweroff}/{$u->hostname()}" title="Apagar equipo {$u->hostname()}"><img src="{$baseurl}/img/poweroff.png" alt="apagar" /></a>
            <a href="{$urlreboot}/{$u->hostname()}" title="Reiniciar equipo {$u->hostname()}"><img src="{$baseurl}/img/reboot.png" alt="reiniciar" /></a>
            <a href="{$urlwakeonlan}/{$u->hostname()}" title="Encender {$u->hostname()}"><img src="{$baseurl}/img/poweron.png" alt="encendido de equipos" /></a>
        </td>
        <td class='tcenter'>
            <a href="{$urlrebootwindows}/{$u->hostname()}" title="Reiniciar equipo '{$u->hostname()}' en Windows"><img src="{$baseurl}/img/windows-logo.jpg" alt="windows" /></a>
            <a href="{$urlrebootmax}/{$u->hostname()}" title="Reiniciar equipo '{$u->hostname()}' en MAX"><img src="{$baseurl}/img/linux-logo.jpg" alt="MAX" /></a>
            <a href="{$urlbackharddi}/{$u->hostname()}" title="Reiniciar equipo '{$u->hostname()}' en Backharddi-NG"><img src="{$baseurl}/img/backharddi-logo.jpg" alt="Backharddi-NG" /></a>
        </td>
      </tr>
      {/if}
      {/foreach}

    </tbody> 
</table> 

<!-- paginador -->
{if $pager}
{$pager->getHTML()}
{/if}


{*
{if $DEBUG}
{debug}
{/if}
*}
