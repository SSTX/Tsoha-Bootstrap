<?php

class BaseController {

    public static function get_user_logged_in() {
        if (isset($_SESSION['userid'])) {
            $user = User::find($_SESSION['userid']);
            return $user;
        }
        return null;
    }

    public static function logged_in(User $userToCheck) {
        return self::get_user_logged_in() == $userToCheck;
    }

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

}
