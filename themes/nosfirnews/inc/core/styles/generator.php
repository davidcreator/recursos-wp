<?php
class NosfirNews_CSS_Generator{
    private $rules=[];
    public function add($sel,$props){ $this->rules[$sel][]=$props; }
    public function render(){ $css=''; foreach($this->rules as $s=>$blocks){ $css.=$s.'{'; foreach($blocks as $d){ foreach($d as $p=>$v){ $css.=$p.':'.$v.';'; } } $css.='}'; } return $css; }
}
