
<h2>Listado de usuarios ({$numusuarios})</h2>



<table class="bDataTable"> 
    <tr> 
        <td> 
        <form id="user" action="{$urlform}" method="post"> 
          <input type='text' name='Filter' id='Filter' value="{$filter}" /> 
          <input type='submit' name='button' value="Buscar" title="Buscar" /> 
          <input type='submit' name='button' value="Añadir usuario" title="Añadir usuario" />
        </form>
        
        </td> 
    </tr> 
</table> 


<table class='dataTable'> 
    <thead> 
      <tr>
      <th class=''>Nombre</th> 
      <th class=''>Nombre completo</th> 
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
