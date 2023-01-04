<?php
if (!function_exists("bind")) {
    function bind($name)
    {
        $str = explode(".", $name);
        if (count($str) > 0) {
            return $str[count($str) - 1];
        }
        return "";
    }
}
