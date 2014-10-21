
<div class="row">
    <div class="col-lg-12">
        {if $faction =='delete'}
        <h1 class="page-header">Borrar usuarios</h1>
        {elseif $faction =='clean'}
        <h1 class="page-header">Limpiar perfiles</h1>
        {/if}
    </div>
    <!-- /.col-lg-12 -->
</div>

<form action='{$urlform}' method='post'> 
    <div class="alert alert-danger">
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
     
     <h1>ATENCIÓN:</h1>
     <h4>Esta operación no se puede deshacer, se borrarán todos los archivos personales de los usuarios seleccionados.</h4>
     <br/><br/>
     
     <input type='hidden' name='usernames' value='{$users}' />
     <input type='hidden' name='faction' value='{$faction}' />
     
     {if $faction =='delete'}
       <div class="form-group">
            <div class="checkbox">
                <label>
                    <input type="checkbox" value="" name='deleteprofile' value='1' checked>
                    Borrar también su perfil y todos sus datos
                </label>
            </div>
        </div>
     {/if}
     <button type="submit" class="btn btn-danger">Confirmar</button>
    </div>
</form>



