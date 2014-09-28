<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Borrar equipo</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<form action='{$urlaction}' method='post'> 
    <div class="alert alert-danger">
     <h2>Se van a borrar los equipos</h2>
     
        <ul>
        {foreach from=$equiposarray item=k}
            <li>{$k}</li>
        {/foreach}
        </ul>
     
     <br/>
     <h4>Esta operación no se puede deshacer</h4>
     <br/>
     
     <input type='hidden' name='equipos' value='{$equipos}' />
     
     <button type="submit" class="btn btn-danger">Confirmar</button>
    </div>
</form>




