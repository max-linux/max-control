<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Apagado o reinicio de equipos</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>



<div class="col-lg-9 alert alert-warning">
 <h2>Se va a {if $action == 'poweroff'}apagar{/if}
             {if $action == 'reboot'}reiniciar{/if}
             {if $action == 'wakeonlan'}encender (WakeOnLAN){/if}
             {if $action == 'rebootwindows'}reiniciar en Windows{/if}
             {if $action == 'rebootmax'}reiniciar en MAX{/if}
 el equipo: {$equipo}</h2>
 <br/><br/>

 <div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
    <thead> 
     <tr>
      <th class="text-center">Nombre</th> 
      <th class="text-center">IP</th>
      <th class="text-center">MAC</th>
     </tr>
    </thead>
 
 
    <tbody> 
      {foreach from=$computers item=c}
      <trid="{$c->hostname()}"> 
        <td class='text-center'>{$c->hostname()}</td> 
        <td class='text-center'>{$c->ipHostNumber}</td> 
        <td class='text-center'>{$c->macAddress}</td>
      </tr>
      {/foreach}

    </tbody> 
</table> 
<a class="btn btn-warning pull-right" href="{$urlaction}">CONTINUAR</a>
</div>
</div>



