<?php

class FileController extends BaseController {

    public static function filelist() {
        $files = File::all();
        View::make('file/filelist.html', array('files' => $files));
    }

    public static function viewFile($id) {
        $file = File::find($id);
        Kint::dump($file);
        View::make('file/viewFile.html', array('file' => $file));
    }

    public static function upload() {
        View::make('file/upload.html');
    }

    public static function editFile($id) {
        $file = File::find($id);
        View::make('file/editFile.html', array('file' => $file));
    }
}
