<?php
$treejavascript = "";

/*
<script language="JavaScript1.2">
<!--

var ns6=document.getElementById&&!document.all?1:0
//var ns6=False;

var head="display:''"
var folder=''

function expandit(curobj){
folder=ns6?curobj.nextSibling.nextSibling.style:document.all[curobj.sourceIndex+1].style
if (folder.display=="none")
folder.display="show"
else
folder.display="none"
}

//-->

</script>
*/

function GetErrorDescription($a_iType, $a_sMsg, $a_sFile, $a_iLine, $a_aContext){

    $l_aErrorTypes = array(E_ERROR=>"E_ERROR",               // used to get a word-based error code
                           E_WARNING=>"E_WARNING",           // instead of the number that will be
                           E_NOTICE=>"E_NOTICE",             // passed to this function
                           E_USER_ERROR=>"E_USER_ERROR",
                           E_USER_WARNING=>"E_USER_WARNING",
                           E_USER_NOTICE=>"E_USER_NOTICE" );
	//echo $l_aErrorTypes[$a_iType];
	//if ($a_iType == E_NOTICE ){
	//		return;
	//	}

    // TABLE 1: Error overview
    $l_sErrorDesc  = "<p><table border='1' cellspacing='5' cellpadding='5' width='90%' bgcolor='#FFFFC0'  align='center'>
        <tr><td><b>Error type:</b></td><td>{$l_aErrorTypes[$a_iType]}</td></tr>\n";
    $l_sErrorDesc .= "<tr><td><b>Message:</b></td><td>$a_sMsg</td></tr>\n";
    $l_sErrorDesc .= "<tr><td><b>Location:</b></td><td>$a_sFile (line: $a_iLine)</tr>\n";
    $l_sErrorDesc .= "</table></p>";

    // TABLE 2: Source code with variable contents tooltips
    $l_sErrorDesc .= "<p><table border='1' cellspacing='5' cellpadding='5' width='90%' bgcolor='#C0C0FF' align='center'>
        <tr><td><pre>\n";
    $l_sCode = file_get_contents($a_sFile,TRUE); // get source code
    $l_aCode = preg_split("#\\n#i",$l_sCode); // split into individual lines
    $l_sDispCode = ""; // this will store the HTML version of the code
    for($i=($a_iLine-15);$i<=($a_iLine+15);$i++){ // display 15 lines either side of the line that caused the error
        if(($i+1)==$a_iLine){ $l_sDispCode .= "<table border=0 bgcolor='#FF8080' cellspacing=0 cellpadding=0 ".
             "width='100%'><tr><td><pre>"; } // add red highlight to the error line
        $l_sDispCode .= "<b>".($i+1)."</b> " . @rtrim($l_aCode[$i]);
        if(($i+1)==$a_iLine){ $l_sDispCode .= "</pre></td></tr></table>"; }
        $l_sDispCode .= "<br />";
    }
    AddToolTips($a_aContext, $l_sDispCode);
    $l_sErrorDesc .= $l_sDispCode . "</pre></td></table></p>";

    // TABLE 3: Global variable dump
    $l_sErrorDesc .= $GLOBALS['treejavascript']; // javascript needed to use collapsable tree
    //$l_sErrorDesc .= "<p><table  border='1' cellspacing='5' cellpadding='5' width='90%' bgcolor='#FFC0C0' align='center'>
    //    <tr><td><b>Variables:</b><br />";
    //$l_sVarDump = ""; // this will store the HTML for the variable tree
    //CreateVarDump($GLOBALS,$l_sVarDump,0);
    //$l_sErrorDesc .= $l_sVarDump . "</td></tr></table></p>";

    // PHPMachine advert (please leave intact)
    //$l_sErrorDesc .= "<p>Do you need help fixing this error? do you need a PHP script modofied, or even developed
    //    from scratch? If so visit <a href='http://www.phpmachine.com'>PHPMachine</a> today for a free no-obligations
    //    quotation.  We guarantee that our price will be the cheapest that you find.</p>";

    return $l_sErrorDesc;
}

function CreateVarDump($variable, &$a_sHTML, $a_iLevel){
    /* Recursive function to create tree like structure of all variables */
    foreach($variable as $l_sVarName=>$l_mValue){
        if($l_sVarName!="GLOBALS"){ // stop infinite loops
            $a_sHTML .= "<h5 style=\"cursor:pointer; cursor:hand\" onClick=\"expandit(this)\">";
            for($i=0;$i<=$a_iLevel;$i++){ $a_sHTML .= "&nbsp;"; }
            $a_sHTML .= "[+] $l_sVarName</h5>";
            //$a_sHTML .= '<span style="display:none" style=&{head};>';
            $a_sHTML .= '<span style=&{head};>';
            if(is_array($l_mValue)||is_object($l_mValue)){
                CreateVarDump($l_mValue,$a_sHTML,($a_iLevel+10));
            } else {
                for($i=0;$i<=$a_iLevel+1;$i++){ $a_sHTML .= "&nbsp;"; }
                $a_sHTML .= htmlspecialchars(strval($l_mValue));
            }
            $a_sHTML .= '</span>';
        }
    }
}

function AddToolTips($a_aContext, &$a_sCode){
    /* Recursive function to add tooltips to variable names displaying their contents */
        foreach($a_aContext as $l_sVarName=>$l_mValue){
            if(is_array($l_mValue) || is_object($l_mValue)){
                AddToolTips($l_mValue, $a_sCode);
                $l_mValue = print_r($l_mValue,TRUE);
		//echo "\n";
            }
            $l_mValue = str_replace("'","&#39;",htmlspecialchars(substr(strval($l_mValue),0,700)));
            $l_sColor = strtoupper("#" . dechex(rand(17,hexdec("BB"))) . dechex(rand(17,hexdec("BB"))) .
                dechex(rand(17,hexdec("BB"))));
            $a_sCode = preg_replace("/\\$$l_sVarName([^a-zA-Z0-9_])/",
                "<b><font color='$l_sColor'><acronym title='$l_mValue'>\$$l_sVarName</acronym></font></b>$1",$a_sCode);
            $a_sCode = str_replace("['$l_sVarName']",
                "<b><font color='$l_sColor'><acronym title='$l_mValue'>['$l_sVarName']</acronym></font></b>",$a_sCode);
        }
}


