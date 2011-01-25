<h2>Apagado o reinicio de aulas</h2>

<form action='{$urlaction}' method='post'> 
    <div class="warning">
      <h2>Se van a {if $faction == 'poweroff'}apagar{/if}
             {if $faction == 'reboot'}reiniciar{/if}
             {if $faction == 'wakeonlan'}encender (WakeOnLAN){/if}
             {if $faction == 'rebootwindows'}reiniciar en Windows{/if}
             {if $faction == 'rebootmax'}reiniciar en MAX{/if}
             {if $faction == 'rebootbackharddi'}reiniciar en Backharddi-NG{/if}
        las aulas:</h2>
     
        <ul>
        {foreach from=$aulasarray item=k}
            <li>{$k}</li>
        {/foreach}
        </ul>
     
     
     <input type='hidden' name='aulas' value='{$aulas}' />
     <input type='hidden' name='faction' value='{$faction}' />
     <input class='inputButton' type='submit' name='confirm' value="Confirmar" alt="Confirmar" />
    </div>
</form>



{*
{if $DEBUG}
{debug}
{/if}
*}
