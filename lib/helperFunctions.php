<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of helperFunctions
 *
 * @author ttiira
 */
class helperFunctions {

    public static function array_flatten($array) {
        $return = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $return = array_merge($return, self::array_flatten($value));
            } else {
                $return[$key] = $value;
            }
        }
        return $return;
    }

    public static function sqlMerge($arr1, $arr2, $delimit = 'INTERSECT') {
        $stmt = array();
        $stmt['stmt'] = $arr1['stmt']. ' ' . $delimit . ' ' . $arr2['stmt'];
        
    }
}
