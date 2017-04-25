<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TagController
 *
 * @author ttiira
 */
class TagController {
    

    public static function updateTags(File $file, $rawTagString) {
        $tags = self::createTags($rawTagString);
        foreach ($file->tags() as $tag) {
            Tag::destroyLink($file, $tag);
        }
        foreach ($tags as $tag) {
            Tag::makeLink($file, $tag);
        }
    }

    private static function createTags($rawTagString) {
        $tagnames = explode(' ', $rawTagString);
        $tags = array();
        foreach ($tagnames as $tagname) {
            $tag = Tag::findByName($tagname);
            if ($tag == null) {
                $tag = new Tag(array('name' => $tagname));
                $tag->save();
            }
            $tags[] = $tag;
        }
        return $tags;
    }

    public static function newTagGet() {
        View::make('tag/newTag.html');
    }

    public static function newTagPost() {
        
    }

    public static function editTagGet() {
        View::make('tag/editTag.html');
    }

    public static function editTagPost() {
        
    }

    public static function tagList() {
        $tags = Tag::all();
        View::make('tag/taglist.html', array('tags' => $tags));
    }

    public static function viewTag($id) {
        $tag = Tag::find($id);
        View::make('tag/viewTag.html', array('tag' => $tag));
    }

}
