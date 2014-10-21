
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Configurar el arranque del aula <u>{$aula->cn}</u></h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <form role="form" action='{$urlform}' method='post'>

                            <div class="form-group form-inline">
                                <label class="col-lg-4">Archivo de arranque</label>
                                <select class='form-control' name='boot' id='boot' > 
                                    <option value=''>Menú de arranque</option> 
                                    {foreach from=$tipos key=k item=o}
                                    <option value='{$k}' {if $aulaboot == $k}selected='selected'{/if}>{$o}</option>
                                    {/foreach}
                                </select> 
                            </div>

                            <div class="form-group form-inline">
                                <label class="col-lg-4">Reiniciar aula</label>
                                <input type='checkbox' class='form-control' name='reboot' value='1' />
                            </div>
                            
                            <input type='hidden' name='aula' value='{$aula->cn}' />
                            <button type="submit" class="btn btn-primary pull-right">Guardar</button>
                        </form>
                    </div>
                    <!-- /.col-lg-6 (nested) -->
                    
                </div>
                <!-- /.row (nested) -->
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
