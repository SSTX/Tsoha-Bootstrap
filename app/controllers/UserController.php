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
        $user = User::authenticate($params['username'], $params['password']);
        if ($user == NULL) {
            View::make('user/login.html', array('err' => 'Invalid username or password',
                'username' => $params['username']));
        } else {
            $_SESSION['user'] = $user;
            Redirect::to('/');
        }
        
    }

    public static function logoutPost() {
        session_unset();
        Redirect::to('/');
    }
    
    public static function registerGet() {
        View::make('user/register.html');
    } 

    public static function registerPost() {
        $params = $_POST;
        $user = User::findByName($params['username']);
        if ($user != null) {
            Redirect::to('/register', array('err' => 'Username is already taken.'));
        }
        $user = new User(array(
            'name' => $params['username'],
            'pwHash' => crypt($params['password']
        ));
        $validator = $user->validator();
        if ($validator->validate()) {
            $user->save();
            session_unset();
            self::loginPost();
        } else {
            View::make("user/register.html", array('errors' => $validator->errors()));
        }
    }
}
