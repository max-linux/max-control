{if $faction =='delete'}
<h2>Borrar usuarios</h2>
{elseif $faction =='clean'}
<h2>Limpiar perfiles</h2>
{/if}

<form action='{$urlform}' method='post'> 
    <div class="warning">
     {if $faction =='delete'}
        <h2>Se van a borrar los siguientes usuarios:
     {elseif $faction =='clean'}
        <h2>Se van a limpiar los perfiles y archivos personales de los siguientes usuarios:
     {/if}
        <ul>
        {foreach from=$usersarray item=k}
            <li>{$k}</li>
        {/foreach}
        </ul>
     </h2>
     
     <h1>ATENCIÓN:</h1> <h4>Esta operación no se puede deshacer, se borrarán todos los archivos personales de los usuarios seleccionados.</h4>
     <br/><br/>
     
     <input type='hidden' name='usernames' value='{$users}' />
     <input type='hidden' name='faction' value='{$faction}' />
     
     {if $faction =='delete'}
       <input type='checkbox' class='inputText' name='deleteprofile' value='1' checked/>
       Borrar también su perfil y todos sus datos
     {/if}
     <input class='inputButton' type='submit' name='confirm' value="Confirmar" alt="Confirmar" />
    </div>
</form>



