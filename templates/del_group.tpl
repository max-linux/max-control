<h2>Borrar grupo</h2>

<form action='{$urlform}' method='post'> 
    <div class="warning">
     <h2>Se va a borrar el grupo "{$group}"</h2>
     
     <h4>Esta operación no se puede deshacer</h4>
     <br/><br/>
     
     <input type='hidden' name='group' value='{$group}' />
     
    <input type='checkbox' class='inputText' name='deleteprofile' value='1' />
    Borrar todos los archivos compartidos
     <input class='inputButton' type='submit' name='confirm' value="Confirmar" alt="Confirmar" />
    </div>
</form>



{if $pruebas}
{debug}
{/if}
