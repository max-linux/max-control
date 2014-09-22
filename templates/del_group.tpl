{if $faction =='delete'}
<h2>Borrar grupo</h2>
{elseif $faction =='deletemembers'}
<h2>Borrar grupo y sus miembros</h2>
{elseif $faction =='clean'}
<h2>Limpiar perfiles de miembros del grupo</h2>
{/if}



<form action='{$urlform}' method='post'> 
    <div class="warning">
     {if $faction =='delete'}
        <h2>Se van a borrar los siguientes grupos:</h2>
     {elseif $faction =='deletemembers'}
        <h2>Se van a borrar los siguientes grupos y sus miembros:</h2>
     {elseif $faction =='clean'}
        <h2>Se van a limpiar los perfiles y archivos personales de los miembros de los siguientes grupos:</h2>
     {/if}
     
        <ul>
        {foreach from=$groupsarray key=k item=u}
            <li>{$k}</li>
            {if $faction != 'delete' && $u->numUsers > 0}
                <ul>
                {foreach from=$u->get_users() item=uu}
                <li>{$uu}</li>
                {/foreach}
                </ul>
            {/if}
        {/foreach}
        </ul>
     
     {if $faction =='delete'}
     <h1>ATENCIÓN:</h1> <h4>Esta operación no se puede deshacer, se borrarán todos los archivos compartidos por el grupo.</h4>
     {elseif $faction =='clean'}
     <h1>ATENCIÓN:</h1> <h4>Esta operación no se puede deshacer, se borrarán todos los archivos personales de los usuarios de los grupos seleccionados.</h4>
     {elseif $faction =='deletemembers'}
     <h1>ATENCIÓN:</h1> <h4>Esta operación no se puede deshacer, se borrarán todos los archivos personales de los usuarios de los grupos seleccionados y los archivos compartidos por el grupo.</h4>
     {/if}
     <br/><br/>
     
     <input type='hidden' name='groups' value='{$groups}' />
     <input type='hidden' name='faction' value='{$faction}' />
     
     {if $faction !='clean'}
       <input type='checkbox' class='inputText' name='deleteprofile' value='1' checked />
       Borrar todos los archivos compartidos
     {/if}
    
     <input class='inputButton' type='submit' name='confirm' value="Confirmar" alt="Confirmar" />
    </div>
</form>


{*
{if $DEBUG}
{debug}
{/if}
*}
