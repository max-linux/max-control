<h2>Apagado o reinicio de equipos</h2>

<form action='{$urlaction}' method='post'> 
    <div class="warning">
      <h2>Se van a {if $faction == 'poweroff'}apagar{/if}
             {if $faction == 'reboot'}reiniciar{/if}
             {if $faction == 'wakeonlan'}encender (WakeOnLAN){/if}
             {if $faction == 'rebootwindows'}reiniciar en Windows{/if}
             {if $faction == 'rebootmax'}reiniciar en MAX{/if}
        los equipos:</h2>
     
        <ul>
        {foreach from=$computersarray item=k}
            <li>{$k}</li>
        {/foreach}
        </ul>
     
     
     <input type='hidden' name='computers' value='{$computers}' />
     <input type='hidden' name='faction' value='{$faction}' />
     <input class='inputButton' type='submit' name='confirm' value="Confirmar" alt="Confirmar" />
    </div>
</form>



{*
{if $DEBUG}
{debug}
{/if}
*}
