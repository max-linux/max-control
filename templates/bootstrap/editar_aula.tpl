
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Administración de los profesores del aula: <span class='stitle'>{$aula}</span></h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<div class="row">
    <form action='{$urlform}' method='post'> 
    
        <div class="col-lg-5">
            <label>Profesores en el aula</label>
            <select name='deluser[]' size='15' multiple class="form-control" data-btn="btnremove"> 
                {foreach from=$miembros.ingroup item=o}
                    <option value="{$o}">{$o}</option> 
                {/foreach}
            </select> 
        </div>
        
        <div class="col-lg-2" style='height:250px;'>
            <div class="span6 text-center center" style='height:50%;'>
                <button type="submit" class="btn btn-primary btn-circle btn-xl btnremove disabled" style="margin-top: 50px;">
                    <i class="fa fa-arrow-right hidden-xs"></i>
                    <i class="fa fa-arrow-down visible-xs"></i>
                </button>
            </div>
            <div class="span6 text-center center" style='height:50%;'>
                <button type="submit" class="btn btn-primary btn-circle btn-xl btnadd disabled" style="margin-top: 50px;">
                    <i class="fa fa-arrow-left hidden-xs"></i>
                    <i class="fa fa-arrow-up visible-xs"></i>
                </button>
            </div>
        </div>
        
        <div class="col-lg-5">
            <label>Resto de profesores</label>
            <select name='adduser[]' size='15' multiple class="form-control" data-btn="btnadd"> 
                {foreach from=$miembros.outgroup item=o}
                    <option value="{$o}">{$o}</option> 
                {/foreach}
            </select> 
            <input type="hidden" name="aula" value="{$aula}"> 
        </div>
    
    </form> 
</div>

{literal}
<script type="text/javascript">
<!--

$(document).ready(function() {
    $('select').on('change', function(event) {
        if( $(this).val() == null || $(this).val().length < 1 ) {
            $("."+ $(this).data('btn') ).addClass('disabled');
        }
        else {
            $("."+ $(this).data('btn') ).removeClass('disabled');
        }
    });
});
-->
</script>
{/literal}

{*

<h3>Administración de los profesores del aula: <span class='stitle'>{$aula}</span></h3> 
 

 
<table class='dashboardTable'> 
<thead> 
	<tr> 
		<th class="tleft">Profesores del aula</th> 
		<th></th> 
		<th class="tleft">Resto de profesores</th> 
	</tr> 
</thead> 
<tbody> 
<tr> 
    <td rowspan="2"> 
    <form action='{$urlform}' method='post'>
        <!-- FIXME soportar añadir y borrado multiple -->
        <select name='deluser[]' size='15' multiple> 
            {foreach from=$miembros.ingroup item=o}
                <option value="{$o}">{$o}</option> 
            {/foreach}
        </select> 
    </td> 
 
    <td> 

    <input class='inputButton' type='image' name='delfromgroup'
            value="Quitar"
            src='{$baseurl}/img/right.gif'
            title="Quitar profesor del aula"
            alt="Quitar profesor del aula" /> 
    <input type="hidden" name="aula" value="{$aula}"/> 
    </form>



    <br /> 
    <br /> 




    <form action='{$urlform}' method='post'> 
    <input class='inputButton' type='image' name='addtogroup'
            value="Añadir usuarios al grupo"
            src='{$baseurl}/img/left.gif'
            title="Añadir profesor al aula"
            alt="Añadir profesor al aula" /> 
    </td> 
 
	<td> 
        <select name='adduser[]' size='15' multiple> 
            {foreach from=$miembros.outgroup item=o}
                <option value="{$o}">{$o}</option> 
            {/foreach}
        </select> 
		<input type="hidden" name="aula" value="{$aula}"> 
    </form> 
	</td> 
</tr> 
</tbody> 
</table>


*}
