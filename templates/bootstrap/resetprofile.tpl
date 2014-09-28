<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Resetear perfil</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<form action='{$urlform}' method='post'> 
    <div class="alert alert-danger">
     <h2>CUIDADO: Se van a borrar todos los archivos del perfil del usuario "{$user}"</h2>
     
     <h4>Esta operación no se puede deshacer</h4>
     <br/><br/>
     
     <input type='hidden' name='user' value='{$user}' />
     
     <button type="submit" class="btn btn-danger">Importar</button>
    </div>
</form>




