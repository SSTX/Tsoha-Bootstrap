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

    private static function userValidator($userData) {
        $v = new Valitron\Validator($userData);
        $v->rule('required', 'name');
        $v->rule('lengthMax', 'name', 30)->message('{field} must be no longer than 30');
        $v->rule('required', 'password');
        $v->rule('equals', 'password', 'passwordRepeat')->message('Passwords don\'t match');
        $v->rule('equals', 'existingUser', null)->message('User already exists');
        $v->labels(array('name' => 'User name'));
        return $v;
    }

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
        $existingUser = User::findByName($params['username']);


        $userData = array(
            'name' => trim($params['username']),
            'password' => $params['password'],
            'passwordRepeat' => $params['passwordRepeat'],
            'existingUser' => $existingUser
        );
        $validator = self::userValidator($userData);
        if ($validator->validate()) {
            //$2a$10$ selects blowfish ($2a$) with 2^10 iterations ($10$)
            $salt = '$2a$10$' . bin2hex(openssl_random_pseudo_bytes(16));
            $user = new User(array(
                'name' => $params['username'],
                'pwHash' => crypt($params['password'], $salt),
                'pwSalt' => $salt));
            $user->save();
            self::loginPost();
        } else {
            $errors = helperFunctions::array_flatten($validator->errors());
            View::make("user/register.html", array('errors' => $errors));
        }
    }

    public static function userlist() {
        $users = User::all();
        View::make('user/userlist.html', array('users' => $users));
    }
    
    public static function userProfile($id) {
        $user = User::find($id);
        View::make('user/userProfile.html', array('user' => $user));
    }
}
