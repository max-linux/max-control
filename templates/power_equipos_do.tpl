<h2>Apagado o reinicio de equipos</h2>

<div class="warning">
 <h2>Se va a {if $action == 'poweroff'}apagar{/if}
             {if $action == 'reboot'}reiniciar{/if}
             {if $action == 'wakeonlan'}encender (WakeOnLAN){/if}
             {if $action == 'rebootwindows'}reiniciar en Windows{/if}
             {if $action == 'rebootmax'}reiniciar en MAX{/if}
 el equipo: {$equipo}</h2>
 <br/><br/>

 <table class='dataTable'> 
    <thead> 
     <tr>
      <th class=''>Nombre</th> 
      <th class=''>IP</th>
      <th class=''>MAC</th>
     </tr>
    </thead>
 
 
    <tbody> 
      {foreach from=$computers item=c}
      <tr class='border' id="{$c->hostname()}"> 
        <td class='tcenter'><span>{$c->hostname()}</span></td> 
        <td class='tcenter'><span>{$c->ipHostNumber}</span></td> 
        <td class='tcenter'><span>{$c->macAddress}</span></td>
      </tr>
      {/foreach}

    </tbody> 
</table> 
 <span class="confirm"> <a href="{$urlaction}">CONTINUAR <img src="{$baseurl}/img/apply.gif" alt="continuar" /></a></span>
</div>



{*
{if $DEBUG}
{debug}
{/if}
*}
