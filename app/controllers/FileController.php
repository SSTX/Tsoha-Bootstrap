<?php

class FileController extends BaseController {

    public static function filelist() {
        $files = File::all();
        View::make('file/filelist.html', array('files' => $files));
    }

    public static function viewFile($id) {
        $file = File::find($id);
        View::make('file/viewFile.html', array('file' => $file));
    }

    public static function uploadGet() {
        View::make('file/upload.html');
    }

    public static function uploadPost() {
        $name =  basename($_FILES['fileInput']['name']);
        $path = 'files/' . $name;
        $size = $_FILES['fileInput']['size'];
        $type = $_FILES['fileInput']['type'];
        $desc = $_POST['fileDescription'];
        $file = new File(array(
            'name' => $name,
            'description' => $desc,
            'size' => $size,
            'path' => $path,
            'type' => $type,
        ));
        move_uploaded_file($_FILES['fileInput']['tmp_name'], $path);
        $file->save();
    }

    public static function editFile($id) {
        $file = File::find($id);
        View::make('file/editFile.html', array('file' => $file));
    }
}
