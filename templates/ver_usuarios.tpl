
<h2>Listado de usuarios ({$numusuarios})</h2>



<table class="bDataTable"> 
    <tr> 
        <td> 
        <form id="formuser" action="{$urlform}" method="post"> 
          <input type='text' name='Filter' id='Filter' value="{$filter}" /> 
          <input type='submit' name='button' value="Buscar" title="Buscar" /> 
          <input type='submit' name='button' value="Añadir usuario" title="Añadir usuario" />
          <select name='role' id='role' onchange="javascript:document.forms.formuser.submit();">
            <option value='' {if $role == ''}selected{/if}>Filtrar por rol</option>
            <option value='alumno' {if $role == 'alumno'}selected{/if}>Alumno</option> 
            <option value='teacher' {if $role == 'teacher'}selected{/if}>Profesor</option> 
            <option value='admin' {if $role == 'admin'}selected{/if}>Administrador</option> 
          </select>
        </form>
        
        </td> 
    </tr> 
</table> 


<table class='dataTable'> 
    <thead> 
      <tr>
      <th class=''>Identificador {$pager->getSortIcons('uid')}</th> 
      <th class=''>Nombre {$pager->getSortIcons('cn')} Apellidos {$pager->getSortIcons('sn')}</th> 
      <th class=''>Rol</th> 
      <th class=''>Cuota</th> 
      <th class=''>Editar</th> 
      <th class=''>Borrar</th> 
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
        <td class='tcenter'> 
            <a href="{$urlborrar}/{$u->attr('uid')}"><img src="{$baseurl}/img/delete.gif" alt="borrar" /></a>
        </td>
      </tr>
      {/foreach}
      

    </tbody> 
</table> 

<!-- paginador -->
{if $pager}
{$pager->getHTML()}
{/if}

{*
{if $DEBUG}
{debug}
{/if}
*}
