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
function smarty_modifier_contains($string, $find='', $cases = false)
{
    $numeros=array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
    $primer=substr($string, 0, 1);
    if( in_array( $primer, $numeros ) ) {
        /* es un entero */
        //echo "es entero '$primer'\n<br/>";
        return 1;
    }
    else {
        //echo "NOOOO es entero '$primer'\n<br/>";
        return 0;
    }
}

/* vim: set expandtab: */

