
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Administración del grupo  <span class='stitle'>{$group}</span></h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<div class="row">
    <form action='{$urlform}' method='post'> 
    
        <div class="col-lg-5">
            <label>Usuarios que pertenecen al grupo</label>
            <select name='deluser[]' size='15' multiple class="form-control" data-btn="btnremove"> 
                {foreach from=$members.ingroup item=o}
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
            <label>Resto de usuarios</label>
            <select name='adduser[]' size='15' multiple class="form-control" data-btn="btnadd"> 
                {foreach from=$members.outgroup item=o}
                    <option value="{$o}">{$o}</option> 
                {/foreach}
            </select> 
            <input type="hidden" name="group" value="{$group}"> 
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


