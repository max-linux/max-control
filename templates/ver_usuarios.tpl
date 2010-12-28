
<h2>Listado de usuarios ({$numusuarios})</h2>



<table class="bDataTable"> 
    <tr> 
        <td> 
        <form id="formuser" action="{$urlform}" method="post"> 
          <input type='text' name='Filter' id='Filter' value="{$filter}" /> 
          <input type='submit' name='button' value="Buscar" title="Buscar" /> 
          <input type='submit' name='button' value="Añadir usuario" title="Añadir usuario" />
          <input type='hidden' name='role' id="role" value='{$role}' />
          <input style="display:none;float:right;" type='button' name='btnDelete' id='btnDelete' 
           value="Borrar seleccionados" title="Borrar seleccionados" onclick="javascript:deleteSelected();"/>
        </form>
        
        </td> 
    </tr> 
</table> 

<form id="formdeletemultipleuser" id="formdeletemultipleuser" action="{$urlformmultiple}" method="post">
    <input type='hidden' name='usernames' id="usernames" value='' />
</form>

<table class='dataTable'> 
    <thead> 
      <tr>
      <th class=''>Identificador {$pager->getSortIcons('uid')}</th> 
      <th class=''>Nombre {$pager->getSortIcons('cn')} Apellidos {$pager->getSortIcons('sn')}</th> 
      <th class=''>Rol
          <select name='selectrole' id='selectrole' onchange="javascript:rolFilter(this);">
            <option value='' {if $role == ''}selected{/if}>----------</option>
            <option value='alumno' {if $role == 'alumno'}selected{/if}>Alumno</option> 
            <option value='teacher' {if $role == 'teacher'}selected{/if}>Profesor</option> 
            <option value='admin' {if $role == 'admin'}selected{/if}>Administrador</option> 
          </select>
      </th> 
      <th class=''>Cuota</th> 
      <th class=''>Editar</th> 
      {*<th class=''>Borrar</th> *}
      <th class=''>Borrar <input class="nomargin" type='checkbox' onchange="javascript:enableAll(this);"/></th>
      </tr>
    </thead>
 
 
    <tbody> 
      {foreach from=$usuarios key=k item=u}
      <tr class='border' id="{$u->attr('uid')}"> 
        <td class='tcenter'><span>{$u->attr('uid')}</span></td> 
        <td class='tcenter'><span>{$u->attr('cn')} {$u->attr('sn')}</span></td> 
        <td class='tcenter'><span>
            {if $u->get_role() == 'teacher'}Profesor{/if}
            {if $u->get_role() == 'admin'}Administrador{/if}
            {if $u->get_role() == ''}Alumno{/if}
                        </span></td> 
        <td class='tcenter'><span>{$u->getquota()}</span></td>
        <td class='tcenter'> 
            <a href="{$urleditar}/{$u->attr('uid')}"><img src="{$baseurl}/img/edit-table.gif" alt="editar" /></a>
        </td>
        {*<td class='tcenter'> 
            <a href="{$urlborrar}/{$u->attr('uid')}"><img src="{$baseurl}/img/delete.gif" alt="borrar" /></a>
        </td>*}
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
        $('#btnDelete')[0].style.display='';
    else
        $('#btnDelete')[0].style.display='none';
}

function deleteSelected(){
    var toDelete=new Array();
    $.each($('.userdel'), function(i) { 
        if ($('.userdel')[i].checked) {
            toDelete.push($('.userdel')[i].id);
            multiple=true;
        }
    });
    $('#usernames')[0].value=toDelete;
    $('#formdeletemultipleuser')[0].submit();
}

function enableAll(obj){
    $.each($('.userdel'), function(i) { 
        $('.userdel')[i].checked=obj.checked;
    });
    if(obj.checked)
        $('#btnDelete')[0].style.display='';
    else
        $('#btnDelete')[0].style.display='none';
}

function rolFilter(obj) {
    $('#role')[0].value=obj.value;
    document.forms.formuser.submit();
}
-->
</script>
{/literal}
{*
{if $DEBUG}
{debug}
{/if}
*}
