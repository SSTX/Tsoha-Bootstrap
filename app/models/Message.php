<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Message
 *
 * @author ttiira
 */
class Message extends BaseModel {

    public $id, $author, $relatedFile, $subject, $body, $submitTime;

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    public static function collect($row) {
        return new Message(array(
            'id' => row['message_id'],
            'author' => User::find($row['message_author']),
            'relatedFile' => File::find(row['message_related_file']),
            'subject' => $row['message_subject'],
            'body' => $row['message_body'],
            'submitTime' => $row['message_submit_time']
        ));
    }

    public static function all() {
        $stmt = 'SELECT * FROM message';
        $query = DB::connection()->prepare($stmt);
        $query->execute();
        $rows = $query->fetchAll();
        $messages = array();
        foreach ($rows as $row) {
            $messages[] = Message::collect($row);
        }
        return $messages;
    }

    public static function find($id) {
        $stmt = 'SELECT * FROM message WHERE message_id = :id';
        $query = DB::connection()->prepare($stmt);
        $query->execute(array('id' => $id));
        $row = $query->fetch();
        $message = NULL;
        if ($row) {
            $message = Message::collect($row);
        }
        return $message;
    }

   

}
