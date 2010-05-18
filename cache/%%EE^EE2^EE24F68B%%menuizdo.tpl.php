<?php /* Smarty version 2.6.22, created on 2010-05-11 17:51:53
         compiled from menuizdo.tpl */ ?>


<!-- menu izdo -->

<?php $_from = $this->_tpl_vars['mainmenu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['menu']):
?>
<li style='display:inline;' > 
    <a title="<?php echo $this->_tpl_vars['menu']; ?>
" href="<?php echo $this->_tpl_vars['basedir']; ?>
/<?php echo $this->_tpl_vars['key']; ?>
" class="navc" target="_parent"><?php echo $this->_tpl_vars['menu']; ?>
</a> 
    <?php if ($this->_tpl_vars['submenu']): ?>
    <?php if ($this->_tpl_vars['key'] == $this->_tpl_vars['module']): ?>
    <ul class='submenu2'> 
        <?php $_from = $this->_tpl_vars['submenu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['subkey'] => $this->_tpl_vars['submenu']):
?>
            <li class='menuUsersAndGroups2'> 
                <a title="<?php echo $this->_tpl_vars['submenu']; ?>
" href="<?php echo $this->_tpl_vars['basedir']; ?>
/<?php echo $this->_tpl_vars['key']; ?>
/<?php echo $this->_tpl_vars['subkey']; ?>
" class="navc" target="_parent"><?php echo $this->_tpl_vars['submenu']; ?>
</a> 
            </li>
        <?php endforeach; endif; unset($_from); ?>
    </ul> 
    <?php endif; ?>
    <?php endif; ?>
</li>
<?php endforeach; endif; unset($_from); ?>

<!-- fin menu izdo -->