<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Apagado o reinicio de equipos</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>


<form action='{$urlaction}' method='post'> 
    <div class="alert alert-warning">
      <h2>Se van a {if $faction == 'poweroff'}apagar{/if}
             {if $faction == 'reboot'}reiniciar{/if}
             {if $faction == 'wakeonlan'}encender (WakeOnLAN){/if}
             {if $faction == 'rebootwindows'}reiniciar en Windows{/if}
             {if $faction == 'rebootmax'}reiniciar en MAX{/if}
        los equipos:</h2>
     
        <ul>
        {foreach from=$computersarray item=k}
            <li>{$k}</li>
        {/foreach}
        </ul>
     <br>
     
     <input type='hidden' name='computers' value='{$computers}' />
     <input type='hidden' name='faction' value='{$faction}' />
     <button type="submit" class="btn btn-danger">Confirmar</button>
    </div>
</form>




