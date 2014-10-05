<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Montar imagen ISO: <u>{$iso}</u></h1>
    </div>
    <!-- /.col-lg-12 -->
</div>


<div class="row">
    <div class="col-lg-12">
        
        <div class="row">
            <div class="col-lg-6">
                <form action='{$urlform}' method='post'> 
                    <label>En el aula</label>
                    <div class="form-group form-inline">
                    <select class="form-control" name='aula' id='aula' > 
                        <option value=''></option> 
                        {foreach from=$aulas item=a}
                            {if $a->teacher_in_aula()}
                                <option value='{$a->attr('cn')}'>{$a->attr('cn')} ({$a->get_num_computers()} equipos)</option>
                            {/if}
                        {/foreach}
                    </select> 
                    <input type='hidden' name='iso' value='{$iso}' />
                    <button type='submit' class="btn btn-primary">Montar en aula</button> 
                    </div>
                    <p class="help-block">Sólo se mostrarán las aulas a las que se tenga acceso</p>

                </form>
            </div>


            <div class="col-lg-6">
                <form action='{$urlform}' method='post'> 
                    <label>En el equipo</label>
                    <div class="form-group form-inline">
                    <select class="form-control" name='equipo' id='equipo' > 
                        <option value=''></option> 
                        {foreach from=$computers item=a}
                            {if $a->teacher_in_computer()}
                                <option value='{$a->hostname()}'>{$a->hostname()}</option>
                            {/if}
                        {/foreach}
                    </select>
                    <input type='hidden' name='iso' value='{$iso}' />
                    <button type='submit' class="btn btn-primary">Montar en el equipo</button> 
                    </div>
                    <p class="help-block">Sólo se mostrarán los equipos de las aulas a las que se tenga acceso</p>

                </form>
            </div>

            <!-- /.col-lg-6 (nested) -->
        </div>
        <!-- /.row (nested) -->
    </div>
    <!-- /.col-lg-12 -->
</div>

