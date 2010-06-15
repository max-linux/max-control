<h2>Borrar equipo</h2>

<form action='{$urlaction}' method='post'> 
    <div class="warning">
     <h2>Se va a borrar el equipo "{$equipo}"</h2>
     
     <h4>Esta operación no se puede deshacer</h4>
     <br/><br/>
     
     <input type='hidden' name='equipo' value='{$equipo}' />
     
     <input class='inputButton' type='submit' name='confirm' value="Confirmar" alt="Confirmar" />
    </div>
</form>



{if $pruebas}
{debug}
{/if}
