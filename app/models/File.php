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

    public static function maxSize() {
        return 5000000;
    }

    public function prettySize() {
        return self::sizeConvert($this->size);
    }

    public static function sizeConvert($bytes) {
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

    public static function search($params) {
        // helper search functions return an array with indexes:
        // 'stmt' => sql statement formatted for PDO
        // 'params' => parameters to send to PDO matching the statement
        $namestmt = self::nameSearch($params['name']);
        $typestmt = self::typeSearch($params['type']);
        $stmt = helperFunctions::sqlMerge($namestmt, $typestmt);
        $idx = 0;
        foreach ($params['tags'] as $tag) {
            $stmt = helperFunctions::sqlMerge($stmt, self::tagSearch($tag, $idx));
            $idx += 1;
        }
        $stmt = helperFunctions::sqlMerge($stmt, self::uploaderSearch($params['uploader']));
        if (empty($stmt)) {
            return self::all();
        }
        $stmt['stmt'] .= ' ORDER BY file_submit_time DESC';
        $query = DB::connection()->prepare($stmt['stmt']);
        $query->execute($stmt['params']);
        $rows = $query->fetchAll();
        $files = array();
        foreach ($rows as $row) {
            $files[] = File::collect($row);
        }
        return $files;
    }

    private static function nameSearch($name) {
        if (empty($name)) {
            return null;
        }
        $name = helperFunctions::sqlReplaceWildcards($name);
        $stmt = 'SELECT file_metadata.* FROM file_metadata WHERE '
                . 'LOWER(file_name) LIKE :name';
        return array('stmt' => $stmt, 'params' => array('name' => $name));
    }

    private static function tagSearch($tag, $idx) {
        if (empty($tag)) {
            return null;
        }
        $tag = helperFunctions::sqlReplaceWildcards($tag);
        $stmt = 'SELECT file_metadata.* FROM file_metadata,tagged_file,tag WHERE '
                . 'file_metadata.file_id = tagged_file.tagged_file '
                . 'AND tag.tag_id = tagged_file.tag '
                . 'AND LOWER(tag.tag_name) LIKE :t' . $idx;
        return array('stmt' => $stmt, 'params' => array('t' . $idx => $tag));
    }

    private static function typeSearch($type) {
        if (empty($type)) {
            return null;
        }
        $type = helperFunctions::sqlReplaceWildcards($type);
        $stmt = 'SELECT file_metadata.* FROM file_metadata WHERE '
                . 'LOWER(file_metadata.file_type) LIKE :type';
        return array('stmt' => $stmt, 'params' => array('type' => $type));
    }

    private static function uploaderSearch($uploader) {
        if (empty($uploader)) {
            return null;
        }
        $uploader = helperFunctions::sqlReplaceWildcards($uploader);
        $stmt = 'SELECT file_metadata.* FROM file_metadata,registered_user '
                . 'WHERE file_metadata.file_author = registered_user.user_id '
                . 'AND registered_user.user_name LIKE :uploader';
        return array('stmt' => $stmt, 'params' => array('uploader' => $uploader));
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
    
    public static function findByUser(User $user) {
        $stmt = 'SELECT file_metadata.* '
                . 'FROM file_metadata,registered_user '
                . 'WHERE file_metadata.file_author = registered_user.user_id '
                . 'AND registered_user.user_id = :userid';
        $query = DB::connection()->prepare($stmt);
        $query->execute(array('userid' => $user->id));
        $rows = $query->fetchAll();
        $files = array();
        foreach ($rows as $row) {
            $files[] = File::collect($row);
        }
        return $files;
    }

    public function save() {
        $stmt = 'INSERT INTO file_metadata '
                . '(file_author, file_name, file_description, '
                . 'file_submit_time, file_path, file_size, file_type) '
                . 'VALUES (:author, :name, :desc, now(), :path, :size, :type) '
                . 'RETURNING file_id';
        $query = DB::connection()->prepare($stmt);
        $uploader = null;
        if ($this->author) {
            $uploader = $this->author->id;
        }
        $query->execute(array('author' => $uploader,
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
        return Message::fileMessageCount($this);
    }

    public function messages() {
        return Message::findByFile($this);
    }

    public function tags() {
        $tags = Tag::linkedTags($this);
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
        if (file_exists(FileController::$basePath . $this->path)) {
            unlink(FileController::$basePath . $this->path);
        }
        Tag::removeOrphans();
    }

    public static function userFileCount(User $user) {
        $stmt = 'SELECT COUNT(*) AS message_count '
                . 'FROM registered_user,file_metadata '
                . 'WHERE registered_user.user_id = file_metadata.file_author '
                . 'AND registered_user.user_id = :userid';
        $query = DB::connection()->prepare($stmt);
        $query->execute(array('userid' => $user->id));
        $row = $query->fetch();
        return $row['message_count'];
    }

}
