<h2>Resetear perfil</h2>

<form action='{$urlform}' method='post'> 
    <div class="warning">
     <h2>CUIDADO: Se van a borrar todos los archivos del perfil del usuario "{$user}"</h2>
     
     <h4>Esta operación no se puede deshacer</h4>
     <br/><br/>
     
     <input type='hidden' name='user' value='{$user}' />
     
     <input class='inputButton' type='submit' name='confirm' value="Confirmar" alt="Confirmar" />
    </div>
</form>



{if $DEBUG}
{debug}
{/if}
