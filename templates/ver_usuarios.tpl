{if $overQuotaEnabled}
<div id="overQuotaDiv" class="warning" style="font-size:10pt;">
    Los siguientes usuarios han superado el {$overQuotaLimit}% de la cuota máxima:
    <ul>
    {foreach from=$overQuota key=k item=u}
        <li>
            <a href="{$urleditar}/{$u.uid}">{$u.uid}</a>
            Usado {$u.size} MB de {$u.maxsize} MB ({$u.percent})
            <a href="{$resetprofilebase}/{$u.uid}"><img src="{$baseurl}/img/delete.gif" alt="borrar" /></a>
        </li>
    {/foreach}
    </ul>
    <span style="float:right;">
        <a href="javascript:void()" onclick="javascript:$('#overQuotaDiv')[0].style.display='none'">ocultar</a>
    </span>
    <br/>
</div>
{/if}


<h2>Listado de usuarios ({$pager->getMAX()})</h2>



<table class="bDataTable"> 
    <tr> 
        <td> 
        <form id="formuser" action="{$urlform}" method="post"> 
          <input type='text' name='Filter' id='Filter' value="{$filter}" /> 
          <input type='submit' name='button' value="Buscar" title="Buscar" /> 
          <input type='submit' name='button' value="Añadir usuario" title="Añadir usuario" />
          <input type='hidden' name='role' id="role" value='{$role}' />
           
           <select style="display:none;float:right;" name='selAction' id='selAction' onchange="javascript:actionSelected();">
            <option value=''>Seleccionar acción...</option>
            <option value='delete'>&nbsp;&nbsp;&nbsp;&nbsp;Borrar seleccionados</option>
            <option value='clean'>&nbsp;&nbsp;&nbsp;&nbsp;Limpiar perfil</option>
           </select>
        </form>
        
        </td> 
    </tr> 
</table> 

<form id="formdeletemultipleuser" name="formdeletemultipleuser" action="{$urlformmultiple}" method="post">
    <input type='hidden' name='usernames' id="usernames" value='' />
    <input type='hidden' name='faction' id="faction" value='' />
</form>

<table class='dataTable'> 
    <thead> 
      <tr>
      <th class=''>Identificador {$pager->getSortIcons('uid')}</th> 
      <th class=''>Nombre {$pager->getSortIcons('cn')} Apellidos {$pager->getSortIcons('sn')}</th> 
      <th class=''>Rol
          <select name='selectrole' id='selectrole' onchange="javascript:rolFilter(this);">
            <option value='' {if $role == ''}selected="selected"{/if}>----------</option>
            <option value='alumno' {if $role == 'alumno'}selected="selected"{/if}>Alumno</option> 
            <option value='teacher' {if $role == 'teacher'}selected="selected"{/if}>Profesor</option> 
            <option value='tic' {if $role == 'tic'}selected="selected"{/if}>Coordinador TIC</option> 
            <option value='admin' {if $role == 'admin'}selected="selected"{/if}>Administrador</option> 
          </select>
      </th> 
      <th class=''>
        {if $quotaTime != ''}
        <acronym title="Cache de cuota generado el: '{$quotaTime}'">Cuota</acronym>
        {else}
        Cuota
         {/if}
        {$pager->getSortIcons('usedSize')}</th> 
      <th class=''>Editar</th> 
      <th class=''>Acciones <input title='Seleccionar todos los visibles' class="nomargin" type='checkbox' onchange="javascript:enableAll(this);"/></th>
      </tr>
    </thead>
 
 
    <tbody> 
      {foreach from=$usuarios key=k item=u}
      <tr class='border' id="user-{$u->attr('uid')}"> 
        {if $u->attr('description') != ''}
        <td class='tcenter'><acronym title='{$u->attr('description')}'><span>{$u->attr('uid')}</span></acronym></td>
        {else}
        <td class='tcenter'><span>{$u->attr('uid')}</span></td> 
        {/if}
        <td class='tcenter'><span>{$u->attr('cn')} {$u->attr('sn')}</span></td> 
        <td class='tcenter'><span>
            {if $u->get_role() == 'teacher'}Profesor{/if}
            {if $u->get_role() == 'tic'}Coordinador TIC{/if}
            {if $u->get_role() == 'admin'}Administrador{/if}
            {if $u->get_role() == ''}Alumno{/if}
                        </span></td> 
        <td class='tcenter'><span>{$u->getquota()}</span></td>
        <td class='tcenter'> 
            <a href="{$urleditar}/{$u->attr('uid')}"><img src="{$baseurl}/img/edit-table.gif" alt="editar" /></a>
        </td>
        <td class='tcenter'> 
            <input type='checkbox' class="userdel" name="{$u->attr('uid')}" id="{$u->attr('uid')}" onchange="javascript:oncheckboxChange();"/>
        </td>
      </tr>
      {/foreach}
      

    </tbody> 
</table> 

<!-- paginador -->
{if $pager}
{$pager->getHTML()}
{/if}

{literal}
<script type="text/javascript">
<!--

function oncheckboxChange() {
    var multiple=false;
    var toDelete=new Array();
    $.each($('.userdel'), function(i) { 
        if ($('.userdel')[i].checked) {
            toDelete.push($('.userdel')[i].id);
            multiple=true;
        }
    });
    if(multiple)
        $('#selAction')[0].style.display='';
    else
        $('#selAction')[0].style.display='none';
}

function enableAll(obj){
    $.each($('.userdel'), function(i) { 
        $('.userdel')[i].checked=obj.checked;
    });
    if(obj.checked)
        $('#selAction')[0].style.display='';
    else
        $('#selAction')[0].style.display='none';
}

function rolFilter(obj) {
    $('#role')[0].value=obj.value;
    document.forms.formuser.submit();
}

function actionSelected(){
    
    var toDelete=new Array();
    $.each($('.userdel'), function(i) { 
        if ($('.userdel')[i].checked) {
            toDelete.push($('.userdel')[i].id);
            multiple=true;
        }
    });
    $('#usernames')[0].value=toDelete;
    
    var faction = $('#selAction').val();
    $('#faction')[0].value=faction;
    $('#formdeletemultipleuser')[0].submit();
}
-->
</script>
{/literal}
{*
{if $DEBUG}
{debug}
{/if}
*}
