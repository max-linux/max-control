
<div class="row">
    <div class="col-lg-12">
        {if $faction =='delete'}
        <h1 class="page-header">Borrar grupo</h1>
        {elseif $faction =='deletemembers'}
        <h1 class="page-header">Borrar grupo y sus miembros</h1>
        {elseif $faction =='clean'}
        <h1 class="page-header">Limpiar perfiles de miembros del grupo</h1>
        {/if}
    </div>
    <!-- /.col-lg-12 -->
</div>

<div class="row">
    <form action='{$urlform}' method='post'> 
        <div class="alert alert-danger">
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
           <div class="form-group">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="" name='deleteprofile' value='1' checked />
                        Borrar todos los archivos compartidos
                    </label>
                </div>
            </div>
         {/if}
         <button type="submit" class="btn btn-danger">Confirmar</button>
        </div>
    </form>
</div>

