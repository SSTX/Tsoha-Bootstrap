<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of userController
 *
 * @author ttiira
 */
class UserController extends BaseController {

    public static function loginGet() {
        View::make('user/login.html');
    }

    public static function loginPost() {
        $params = $_POST;
        $userid = User::authenticate($params['username'], $params['password']);
        if ($userid == NULL) {
            View::make('user/login.html', array('err' => 'Invalid username or password',
                'username' => $params['username']));
        } else {
            $_SESSION['userid'] = $userid;
            $user = User::find($userid);
            Redirect::to('/', array('successMsg' => 'Logged in as ' . $user->name));
        }
    }

    public static function logoutGet() {
        $user = self::get_user_logged_in();
        $name;
        if ($user == null) {
            $name = '';
        } else {
            $name = ' ' . $user->name;
        }
        session_unset();
        Redirect::to('/', array('infoMsg' => 'Logged out' . $name . '.'));
    }

    public static function registerGet() {
        View::make('user/register.html');
    }

    public static function registerPost() {
        $params = $_POST;
        $found = User::findByName($params['username']);
        if ($found != null) {
            Redirect::to('/register', array('err' => 'Username is already taken.'));
        }
        if ($_POST['password'] != $_POST['passwordRepeat']) {
            Redirect::to('/register', array('err' => 'Passwords don\'t match.'));
        }
        //$2a$10$ selects blowfish ($2a$) with 2^10 iterations ($10$)
        $salt = '$2a$10$' . bin2hex(openssl_random_pseudo_bytes(16));
        $user = new User(array(
            'name' => $params['username'],
            'pwHash' => crypt($params['password'], $salt),
            'pwSalt' => $salt));
        $validator = $user->validator();
        if ($validator->validate()) {
            $user->save();
            self::loginPost();
        } else {
            View::make("user/register.html", array('errors' => $validator->errors()));
        }
    }

}
