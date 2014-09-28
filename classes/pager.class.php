<?php
if(DEBUG)
    error_reporting(E_ALL);

class PAGER {
    function PAGER($items, $baseurl, $skip, $args='', $sort=NULL) {
        global $gui;
        $this->items=$items;
        $this->number=sizeof($items);
        $this->baseurl=$baseurl;
        $this->args=$args;
        $this->sort=$sort;
        $this->sortfilter="(uid|cn|sn|numUsers)";
        $this->pagerLimit=PAGER_LIMIT;

        if ($skip == "") {
            $this->skip=0;
        }
        else {
            $this->skip=intval($skip);
        }

        /*
        $gui->debug("<pre>PAGER number=".$this->number." max=".$this->pagerLimit." baseurl=$baseurl <br/>
        skip=".$this->skip." <br/>
        args=".$this->args." <br/>
        sort=".print_r($sort,true)."</pre>");*/
        return;
    }

    function getMAX() {
        return $this->number;
    }

    
    function processArgs($argsarray) {
        global $gui;

        foreach($argsarray as $argname) {
            $argvalue=leer_datos($argname);

            if ($argname == 'sort' && $argvalue != '') {
                $sortmode=leer_datos('mode');
                if($sortmode=="dsc") {
                    $this->sort=array($argvalue, SORT_DESC);
                    $this->args.="&amp;sort=$argvalue&amp;mode=dsc";
                }
                else {
                    $this->sort=array($argvalue, SORT_ASC);
                    $this->args.="&amp;sort=$argvalue&amp;mode=asc";
                }
                // break loop $this->args edited
                continue;
            }
            elseif($argname == 'skip' && $argvalue != '') {
                $this->skip=intval($argvalue);
            }

            if($argvalue != '') {
                $this->args.="&amp;$argname=$argvalue";
            }
        }
        
        // $gui->debug("<pre>PAGER number=".$this->number." max=".$this->pagerLimit." baseurl=$this->baseurl
        // skip=".$this->skip."
        // args=".$this->args."
        // sort=".print_r($this->sort,true)."</pre>");
    }


    function needPager() {
        return $this->number > $this->pagerLimit;
    }

    
    function getHTML() {
        if ( $this->number <= $this->pagerLimit ){
            return "";
        }
        global $gui;

        $total_pages=intval($this->number/$this->pagerLimit);
        $resto=$this->number%$this->pagerLimit;
        if ($resto > 0) {
            $total_pages++;
        }

        $newargs = preg_replace("/&skip=([0-9]+)/",'',$this->args);
        $newargs = preg_replace("/&amp;skip=([0-9]+)/",'',$newargs);
        $newargs = preg_replace("/\?skip=([0-9]+)/",'',$newargs);

        //$gui->debuga($newargs);

        //$gui->debug("<pre>PAGER number=".$this->number." total_pages=$total_pages resto=$resto</pre>");
        $html="<div class='pages'>";

        if ( ($this->skip-$this->pagerLimit)>= 0 ) {
            $html.="&nbsp;&nbsp;<a class='nextprev' href='".$this->baseurl."$newargs'>« Primero</a>";
            $html.="&nbsp;&nbsp;<a class='nextprev' href='".$this->baseurl."?skip=".($this->skip - $this->pagerLimit)."$newargs'>« Anterior</a>";
        }
        else {
            $html.="&nbsp;&nbsp;<span class='nextprev'>« Anterior</span>";
        }
        // based on http://www.smarty.net/forums/viewtopic.php?p=12747&sid=941343163ff96ced4e8bcbac6fe3f4c3 
        $delta_l = 0;
        $delta_r = 0;
        if ($this->pagerLimit % 2 == 0) {
            $delta_l = (PAGER_MAX_LINKS / 2 ) - 1;
            $delta_r = PAGER_MAX_LINKS / 2;
        } else {
            $delta_l = $delta_r = (PAGER_MAX_LINKS - 1) / 2;
        }

        $links = array();
        for($i = 0; $i < $total_pages; $i++) {
            $links[$i] = $i + 1;
        }
        //$gui->debuga($links);

        $int_curpage=$this->skip/$this->pagerLimit;
        $linknum=PAGER_MAX_LINKS;
        $pagecount=$total_pages;

        if (($int_curpage - $delta_l) < 1) { // Delta_l needs adjustment, we are too far left
            $delta_l = $int_curpage - 1;
            $delta_r = $linknum - $delta_l - 1;
        }
        if (($int_curpage + $delta_r) > $pagecount) { // Delta_r needs adjustment, we are too far right
            $delta_r = $pagecount - $int_curpage;
            $delta_l = $linknum - $delta_r - 1;
        }
        if ($int_curpage - $delta_l > 1) { // Let's do some cutting on the left side
            array_splice($links, 0, $int_curpage - $delta_l);
        }
        if ($int_curpage + $delta_r < $pagecount) { // The right side will also need some treatment
            array_splice($links, $int_curpage + $delta_r + 2 - $links[0]);
        }

        //$gui->debuga($links);

        for($i=0; $i<$total_pages; $i++) {
            $skipcount=$i*$this->pagerLimit;

            if ($this->skip == $skipcount) {
                $html.="&nbsp;&nbsp;<span class='current'>".($i+1)."</span>";
            }
            else{
                if ( in_array($i+1, $links) ) {
                    //$gui->debug("i=$i en array() curpage=$int_curpage");
                    $html.="&nbsp;&nbsp;<a class='pagerLink' href='".$this->baseurl."?skip=".$skipcount.$newargs."'>".($i+1)."</a>";
                }
                
            }
        }
        if ( ($this->skip+$this->pagerLimit)< $this->number ) {
            $html.="&nbsp;&nbsp;<a class='nextprev' href='".$this->baseurl."?skip=".($this->skip+$this->pagerLimit).$newargs."'>Siguiente »</a>";
            $last=($total_pages-1)*$this->pagerLimit;
            $html.="&nbsp;&nbsp;<a class='nextprev' href='".$this->baseurl."?skip=".$last.$newargs."'>Último »</a>";
        }
        else {
            $html.="&nbsp;&nbsp;<span class='nextprev'>Siguiente »</span>";
        }

        $html.="<br style='clear:both' /></div>";
        return $html;
    }
    




    function getItems() {
        global $gui;
        global $sort_opts;
        if ($this->sort) {
            $sort_opts=$this->sort;
            usort($this->items, array(&$this, "compareObjects"));
        }
        /* array array_slice ( array $array , int $offset [, int $length [, bool $preserve_keys = false ]] ) */
        $this->items=array_slice($this->items, $this->skip, $this->pagerLimit, $preserve_keys = false );
        return $this->items;
    }

    static function compareObjects($a, $b) {
        global $gui;
        global $sort_opts;
        $value=$sort_opts[0];
        $reverse=1;
        if ($sort_opts[1] == SORT_DESC)
            $reverse=-1;

        /*
        $gui->debug("<pre>'".$a->$value."'<=>'".$b->$value."'<pre>");
        //$gui->debuga($b->$value);
        $gui->debug("<hr>");
        */

        if( isset($a->$value) && ! isset($b->$value) ) {
            return 1*$reverse;
        }

        if( ! isset($a->$value) && isset($b->$value) ) {
            return -1*$reverse;
        }

        if( ! isset($a->$value) && ! isset($b->$value) ) {
            return 0;
        }

        if ($a->$value == $b->$value) {
            return 0;
        } else {
            if ( $a->$value < $b->$value ) {
                return -1*$reverse;
            }
            else {
                return 1*$reverse;
            }
        }
    }

    function getSortIcons($filter) {
        /*
        &#9650; ▲ &#9651; △
        &#9660; ▼ &#9661; ▽
        */
        global $gui;
        global $site;
        $down="&#9661;";
        $up="&#9651;";

        if ( preg_match("/&sort=$filter&mode=asc/", $this->args) ||
             preg_match("/&amp;sort=$filter&amp;mode=asc/", $this->args)) {
            $up="&#9650;";
        }
        if ( preg_match("/&sort=$filter&mode=dsc/", $this->args) ||
             preg_match("/&amp;sort=$filter&amp;mode=dsc/", $this->args)) {
            $down="&#9660;";
        }

        /* clean $this->args */
        $newargs = preg_replace("/&sort=".$this->sortfilter."&mode=(asc|dsc)/",'',$this->args);
        $newargs = preg_replace("/&amp;sort=".$this->sortfilter."&amp;mode=(asc|dsc)/",'',$newargs);
        /*$gui->debug("getSortIcons($filter) this->args='".$this->args."' => newargs='$newargs'");*/

        $html="\n";
        /* iconos */
        $html.="<a title='Ordenar ascendentemente por \"$filter\"' class='sortlink' href='".$this->baseurl."?sort=$filter&amp;mode=asc".$newargs."'>$up</a>\n";
        $html.="<a title='Ordenar descendentemente por \"$filter\"' class='sortlink' href='".$this->baseurl."?sort=$filter&amp;mode=dsc".$newargs."'>$down</a>";
        return $html;
    }
}

