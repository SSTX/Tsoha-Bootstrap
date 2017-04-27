<?php

class BaseController {

    public static function get_user_logged_in() {
        if (isset($_SESSION['userid'])) {
            $user = User::find($_SESSION['userid']);
            return $user;
        }
        return null;
    }

    public static function checkOwnership($owner) {
        $user = self::get_user_logged_in();
        return $user != null && ($user->isAdmin || $user == $owner);
    }
}
