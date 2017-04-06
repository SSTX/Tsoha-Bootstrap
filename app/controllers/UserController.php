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
        View::Make
    }
    public static function loginPost() {
        $params = $_POST;
        $user = User::authenticate($params['username'], $params['password']);
        if ($user == NULL) {
            View::make('user/login.html', array('error' => 'Invalid username or password',
                'username' => $params['username']));
        }
    }
    
    
}
