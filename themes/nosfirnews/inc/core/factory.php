<?php
class NosfirNews_Factory{
    public static function create($class,$args=[]){ if(class_exists($class)) return new $class($args); return null; }
}
