<h2>Borrar aula</h2>

<form action='{$urlform}' method='post'> 
    <div class="warning">
     <h2>Se va a borrar el aula "{$aula}"</h2>
     
     <h4>Esta operación no se puede deshacer</h4>
     <br/><br/>
     
     <input type='hidden' name='aula' value='{$aula}' />
     
     <input class='inputButton' type='submit' name='confirm' value="Confirmar" alt="Confirmar" />
    </div>
</form>



{if $pruebas}
{debug}
{/if}
