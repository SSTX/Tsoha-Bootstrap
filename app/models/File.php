<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of File
 *
 * @author ttiira
 */
class File extends BaseModel {

    public $id, $author, $name, $description, $submitTime, $path, $size, $type;

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    private static function collect($row) {
        $uploader = User::find($row['file_author']);
        return new File(array(
            'id' => $row['file_id'],
            'author' => $uploader,
            'name' => $row['file_name'],
            'description' => $row['file_description'],
            'submitTime' => $row['file_submit_time'],
            'path' => $row['file_path'],
            'size' => $row['file_size'],
            'type' => $row['file_type']
        ));
    }

    public function validator() {
        $v = new Valitron\Validator(get_object_vars($this));
        $v->rule('required', array('name'));
        $v->rule('optional', array('id', 'size'));
        $v->rule('integer', array('id', 'size'));
        $v->rule('max', 'size', 3000000);
        return $v;
    }

    public function prettySize() {
        $bytes = $this->size;
        $mul = 0;
        while ($bytes >= 1000) {
            $bytes = $bytes / 1000;
            $mul = $mul + 1;
        }
        $unit = 'GB';
        if ($mul == 0) {
            $unit = 'B';
        } else if ($mul == 1) {
            $unit = 'kB';
        } else if ($mul == 2) {
            $unit = 'MB';
        }
        $bytes = round($bytes, 1);
        return $bytes . ' ' . $unit;
    }

    public function prettySubmitTime() {
        return date('Y-m-d H:i:s', strtotime($this->submitTime));
    }

    public static function all() {
        $stmt = 'SELECT * FROM file_metadata';
        $query = DB::connection()->prepare($stmt);
        $query->execute();
        $rows = $query->fetchAll();
        $files = array();
        foreach ($rows as $row) {
            $files[] = File::collect($row);
        }
        return $files;
    }

    public static function find($id) {
        $stmt = 'SELECT * FROM file_metadata WHERE file_id = :id';
        $query = DB::connection()->prepare($stmt);
        $query->execute(array('id' => $id));
        $row = $query->fetch();
        $file = NULL;
        if ($row) {
            $file = File::collect($row);
        }
        return $file;
    }

    public function save() {
        $stmt = 'INSERT INTO file_metadata '
                . '(file_author, file_name, file_description, '
                . 'file_submit_time, file_path, file_size, file_type) '
                . 'VALUES (:author, :name, :desc, now(), :path, :size, :type) '
                . 'RETURNING file_id';
        $query = DB::connection()->prepare($stmt);
        $query->execute(array('author' => $this->author,
            'name' => $this->name,
            'desc' => $this->description,
            'path' => $this->path,
            'size' => $this->size,
            'type' => $this->type
        ));
        $row = $query->fetch();
        $this->id = $row['file_id'];
    }

    public function messageCount() {
        $stmt = 'SELECT COUNT(*) AS message_count FROM message WHERE '
                . 'message_related_file = :fileId';
        $query = DB::connection()->prepare($stmt);
        $query->execute(array('fileId' => $this->id));
        $row = $query->fetch();
        return $row['message_count'];
    }

    public function messages() {
        return Message::findByFile($this);
    }

    public function tags() {
        $stmt = 'SELECT tag.* FROM tag,tagged_file,file_metadata WHERE '
            .'tagged_file.tagged_file = file_metadata.file_id '
            .'AND tagged_file.tag = tag.tag_id '
            .'AND file_metadata.file_id = :id';
        $query = DB::connection()->prepare($stmt);
        $query->execute(array('id' => $this->id));
        $rows = $query->fetchAll();
        $tags = array();
        foreach($rows as $row) {
            $tags[] = Tag::collect($row);
        }
        return $tags;
    }

    public function update() {
        $stmt = 'UPDATE file_metadata '
                . 'SET file_description = :desc, '
                . 'file_name = :name '
                . 'WHERE file_id = :id';
        $query = DB::connection()->prepare($stmt);
        $query->execute(array('id' => $this->id,
            'name' => $this->name,
            'desc' => $this->description));
    }
    
    public function destroy() {
        $stmt = 'DELETE FROM file_metadata '
                . 'WHERE file_id = :id';
        $query = DB::connection()->prepare($stmt);
        $query->execute(array('id' => $this->id));
    }
}
