<h2>Borrar grupo</h2>

<form action='{$urlform}' method='post'> 
    <div class="warning">
     <h2>Se van a borrar los siguientes grupos:</h2>
     
        <ul>
        {foreach from=$groupsarray item=k}
            <li>{$k}</li>
        {/foreach}
        </ul>
     
     <h4>Esta operación no se puede deshacer</h4>
     <br/><br/>
     
     <input type='hidden' name='groups' value='{$groups}' />
     
    <input type='checkbox' class='inputText' name='deleteprofile' value='1' checked />
    Borrar todos los archivos compartidos
     <input class='inputButton' type='submit' name='confirm' value="Confirmar" alt="Confirmar" />
    </div>
</form>



{if $DEBUG}
{debug}
{/if}
