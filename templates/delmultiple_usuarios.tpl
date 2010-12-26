<h2>Borrar usuarios</h2>

<form action='{$urlform}' method='post'> 
    <div class="warning">
     <h2>Se van a borrar los siguientes usuarios:
        <ul>
        {foreach from=$usersarray item=k}
            <li>{$k}</li>
        {/foreach}
        </ul>
     </h2>
     
     <h4>Esta operación no se puede deshacer</h4>
     <br/><br/>
     
     <input type='hidden' name='usernames' value='{$users}' />
     
    <input type='checkbox' class='inputText' name='deleteprofile' value='1' checked/>
    Borrar también su perfil y todos sus datos
     <input class='inputButton' type='submit' name='confirm' value="Confirmar" alt="Confirmar" />
    </div>
</form>



{if $DEBUG}
{debug}
{/if}
