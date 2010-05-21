<h2>Apagado o reinicio de aulas</h2>

<div class="warning">
 <h2>Se va a {if $action == 'poweroff'}apagar{/if}{if $action == 'reboot'}reiniciar{/if} el aula: {$aula}</h2>
 <br/><br/>

 <table class='dataTable'> 
    <thead> 
      <th class=''>Nombre</th> 
      <th class=''>IP</th>
      <th class=''>MAC</th>
    </thead>
 
 
    <tbody> 
      {foreach from=$computers item=c}
      <tr class='border' id="{$u->cn}"> 
        <td class='tcenter'><span>{$c->hostname()}</span></td> 
        <td class='tcenter'><span>{$c->ipHostNumber}</span></td> 
        <td class='tcenter'><span>{$c->macAddress}</span></td>
      </tr>
      {/foreach}

    </tbody> 
</table> 
 <span class="confirm"> <a href="{$urlaction}">SEGUIR <img src="{$baseurl}/img/apply.gif"></a></span>
</div>




{if $pruebas}
{debug}
{/if}
