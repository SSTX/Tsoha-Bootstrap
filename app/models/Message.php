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
            'id' => $row['message_id'],
            'author' => User::find($row['message_author']),
            'relatedFile' => File::find($row['message_related_file']),
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

    public static function findByFile($file) {
        $stmt = 'SELECT * FROM message WHERE '
                . 'message_related_file = :id';
        $query = DB::connection()->prepare($stmt);
        $query->execute(array('id' => $file->id));
        $rows = $query->fetchAll();
        $messages = array();
        foreach ($rows as $row) {
            $messages[] = Message::collect($row);
        }
        return $messages;
    }

    public static function fileMessageCount(File $file) {
        $stmt = 'SELECT COUNT(*) AS message_count FROM message WHERE '
                . 'message_related_file = :fileId';
        $query = DB::connection()->prepare($stmt);
        $query->execute(array('fileId' => $file->id));
        $row = $query->fetch();
        return $row['message_count'];
    }
    
    public static function userMessageCount(User $user) {
        $stmt = 'SELECT COUNT (*) AS message_count '
                . 'FROM registered_user,file_metadata,message '
                . 'WHERE file_metadata.file_id = message.related_file '
                . 'AND registered_user.user_id = file_metadata.file_author '
                . 'AND registered_user.user_id = :userid';
        $query = DB::connection()->prepare($stmt);
        $query->execute(array('userid' => $user->id));
        $row = $query->fetch();
        return $row['message_count'];
    }

    public function save() {
        $stmt = 'INSERT INTO message '
                . '(message_author, message_related_file, '
                . 'message_subject, message_body, message_submit_time) '
                . 'VALUES (:authorId, :fileId, :subject, :body, now()) '
                . 'RETURNING message_id';
        $query = DB::connection()->prepare($stmt);
        $params = array(
            'fileId' => $this->relatedFile->id,
            'authorId' => null,
            'subject' => $this->subject,
            'body' => $this->body
        );
        if (!empty($this->author)) {
            $params['authorId'] = $this->author->id;
        }
        $query->execute($params);
        $row = $query->fetch();
        $this->id = $row['message_id'];
    }

    public function destroy() {
        $stmt = 'DELETE FROM message WHERE message_id = :id';
        $query = DB::connection()->prepare($stmt);
        $query->execute(array('id' => $this->id));
    }

}
