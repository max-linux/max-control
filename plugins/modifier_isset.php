<?php
/**
 * Smarty shared plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Function: smarty_contains
 * Purpose:  Used to find a string in a string
 * Example: contains( 'Jason was here', 'here' ) returns true
 * Example2: contains( 'Jason was here', 'ason' ) returns false
 * @author Jason Strese <Jason dot Strese at gmail dot com>
 * @param string
 * @return string
 */
function smarty_modifier_isset($var)
{
    if ( isset($var) ) {
       return 1;
    }
    else {
       return 0;
    }
}

/* vim: set expandtab: */

