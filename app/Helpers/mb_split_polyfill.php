<?php

if (!function_exists('mb_split')) {
    function mb_split($pattern, $string) {
        return preg_split('/' . $pattern . '/u', $string);
    }
}