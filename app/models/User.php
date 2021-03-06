<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author ttiira
 */
class User extends BaseModel {
    public $id, $name, $pwHash, $pwSalt, $registerTime, $isAdmin;
    
    public function __construct($attributes) {
        parent::__construct($attributes);
    }
    
    public static function collect($row) {
        return new User(array(
            'id' => $row['user_id'],
            'name' => $row['user_name'],
            'pwHash' => $row['user_pw_hash'],
            'pwSalt' => $row['user_pw_salt'],
            'registerTime' => $row['user_register_time'],
            'isAdmin' => $row['user_is_admin']
        ));
    }
    
    public static function all() {
        $stmt = 'SELECT * FROM registered_user';
        $query = DB::connection()->prepare($stmt);
        $query->execute();
        $rows = $query->fetchAll();
        $users = array();
        foreach ($rows as $row) {
            $users[] = User::collect($row);
        }
        return $users;
    }
    
    public static function find($id) {
        $stmt = 'SELECT * FROM registered_user WHERE '
                . 'user_id = :id';
        $query = DB::connection()->prepare($stmt);
        $query->execute(array('id' => $id));
        $row = $query->fetch();
        $user = NULL;
        if ($row) {
            $user = User::collect($row);
        }
        return $user;
    }

    public static function findByName($name) {
        $stmt = 'SELECT * FROM registered_user WHERE '
                . 'user_name = :name';
        $query = DB::connection()->prepare($stmt);
        $query->execute(array('name' => $name));
        $row = $query->fetch();
        $user = NULL;
        if ($row) {
            $user = User::collect($row);
        }
        return $user;
    }
    
    public function save() {
        $stmt = 'INSERT INTO registered_user (user_name, user_pw_hash, user_pw_salt) '
                . 'VALUES (:name, :pwHash, :pwSalt) '
                . 'RETURNING user_id';
        $query = DB::connection()->prepare($stmt);
        $query->execute(array(
            'name' => $this->name,
            'pwHash' => $this->pwHash,
            'pwSalt' => $this->pwSalt));
        $row = $query->fetch();
        $this->id = $row['user_id'];
    }
    
    public static function authenticate($username, $password) {
        $stmt = 'SELECT * FROM registered_user '
            . 'WHERE user_name = :name';
        $query = DB::connection()->prepare($stmt);
        $query->execute(array('name' => $username));
        $row = $query->fetch();
        if ($row) {
            $user = User::collect($row);
            $hash = crypt($password, $user->pwSalt);
            if ($hash != $user->pwHash) {
                return null;
            }
            return $user->id;
        }
        return NULL;
    }
    
    public function update() {
        $stmt = 'UPDATE registered_user '
                . 'SET user_pw_hash = :hash, '
                . 'user_pw_salt = :salt '
                . 'WHERE user_id = :id';
        $query = DB::connection()->prepare($stmt);
        $query->execute(array(
            'hash' => $this->pwHash,
            'salt' => $this->pwSalt,
            'id' => $this->id
        ));
    }
    
    public function destroy() {
        $stmt = 'DELETE FROM registered_user '
                . 'WHERE user_id = :userid';
        $query = DB::connection()->prepare($stmt);
        $query->execute(array('userid' => $this->id));
    }
    
    public function ownedFiles() {
        return File::findByUser($this);
    }
    
    public function messageCount() {
        return Message::userMessageCount($this);
    }
    
    public function fileCount() {
        return File::userFileCount($this);
    }
}
