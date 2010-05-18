{* Smarty *}

{* debug.tpl HTML 4.01 Strict *}

{assign_debug_info}

    

	<table class="debug_table">
	<tr class="debug_tr" >
	    <th colspan=2>Smarty Debug Console</th>
	</tr>
	
	<tr class="debug_tr">
	    <td colspan='2'><b>included templates &amp; config files (load time in seconds):</b></td>
	</tr>
	
	{section name=templates loop=$_debug_tpls}
		<tr class="{if %templates.index% is even}even{else}not_even{/if}">
		    <td colspan='2'>
		        <tt>{section name=indent loop=$_debug_tpls[templates].depth}&nbsp;&nbsp;&nbsp;{/section}
		        <span class='{if $_debug_tpls[templates].type eq "template"}brown{elseif $_debug_tpls[templates].type eq "insert"}black{else}green{/if}'>
		        {$_debug_tpls[templates].filename|escape:html}</span>
		        
		        {if isset($_debug_tpls[templates].exec_time)} 
		        <small><i>({$_debug_tpls[templates].exec_time|string_format:"%.5f"}){if %templates.index% eq 0} (total){/if}</i></small>{/if}</tt>
		    </td>
		</tr>
	{sectionelse}
		<tr class="even">
		    <td colspan='2'><tt><i>no templates included</i></tt></td>
		</tr>	
	{/section}
	
	
	<tr class="debug_tr">
	    <td colspan='2'><b>assigned template variables:</b></td>
	</tr>
	
	{section name=vars loop=$_debug_keys}
		<tr class="{if %vars.index% is even}even{else}not_even{/if}">
		    <td valign=top>
		        <tt><span class="blue">{ldelim}${$_debug_keys[vars]}{rdelim}</span></tt>
		    </td>
		    <td>
		        <tt><span class="green">{$_debug_vals[vars]|@debug_print_var}</span></tt>
		    </td>
		</tr>
		
	{sectionelse}
		<tr class="even">
		    <td colspan='2'>
		        <tt><i>no template variables assigned</i></tt>
		    </td>
		</tr>	
	{/section}
	
	
	<tr class="debug_tr">
	    <td colspan='2'><b>assigned config file variables (outer template scope):</b></td>
	</tr>
	
	{section name=config_vars loop=$_debug_config_keys}
		<tr class="{if %vars.index% is even}even{else}not_even{/if}">
		    <td valign=top>
		        <tt><span class="brown">{ldelim}#{$_debug_config_keys[config_vars]}#{rdelim}</span></tt>
		    </td>
		    
		    <td>
		        <tt><span class="green">{$_debug_config_vals[config_vars]|@debug_print_var}</span></tt>
		    </td>
	    </tr>
	    
	{sectionelse}
		<tr class="even"><td colspan=2><tt><i>no config vars assigned</i></tt></td></tr>	
	{/section}
	
	</table>
	
</body>	
</html>
