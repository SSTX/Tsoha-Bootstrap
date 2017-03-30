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
class Tag extends BaseModel {

    public $id, $name, $description;

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    private static function collect($row) {
        return new Tag(array(
            'id' => $row['tag_id'],
            'name' => $row['file_name'],
            'description' => $row['tag_description']
        ));
    }

    public static function all() {
        $stmt = 'SELECT * FROM tag';
        $query = DB::connection()->prepare($stmt);
        $query->execute();
        $rows = $query->fetchAll();
        $tags = array();
        foreach ($rows as $row) {
            $tags[] = Tag::collect($row);
        }
        return $tags;
    }

    public static function find($id) {
        $stmt = 'SELECT * FROM tag WHERE tag_id = :id';
        $query = DB::connection()->prepare($stmt);
        $query->execute(array('id' => $id));
        $row = $query->fetch();
        $tag = NULL;
        if ($row) {
            $tag = tag::collect($row);
        }
        return $tag;
    }

    public function save() {
        $stmt = 'INSERT INTO tag (tag_name, tag_description) '
            .'VALUES (:name, :desc) '
            .'RETURNING tag_id';
        $query = DB::connection()->prepare($stmt);
        $query->execute(array(
            'name' => $this->name,
            'desc' => $this->description,
        ));
        $row = $query->fetch();
        $this->id = $row['file_id'];
    }

}
