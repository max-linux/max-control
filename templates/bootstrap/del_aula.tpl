<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Borrar aula</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<form action='{$urlform}' method='post'> 
    <div class="alert alert-danger">
     <h2>Se va a borrar el aula "{$aula}"</h2>
     
     <h4>Esta operación no se puede deshacer</h4>
     <br/><br/>
     
     <input type='hidden' name='aula' value='{$aula}' />
     
     <button type="submit" class="btn btn-danger">Confirmar</button>
    </div>
</form>


{*
{if $DEBUG}
{debug}
{/if}
*}
