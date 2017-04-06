<?php

class FileController extends BaseController {
    public static $baseFilePath = '/home/ttiira/htdocs/files/';
    
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
        $movepath = FileController::$baseFilePath . $name;
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
        $validator = $file->validator();
        if ($validator->validate()) {
            move_uploaded_file($_FILES['fileInput']['tmp_name'], $movepath);
            chmod($movepath, 0744);
            $file->save();
            Redirect::to('/file/' . $file->id);
        } else {
            Redirect::to('/upload', array('file' => $file, 'errors' => $validator->errors()));
        }
        
    }

    public static function editFileGet($id) {
        $file = File::find($id);
        View::make('file/editFile.html', array('file' => $file));
    }
    
    public static function editFilePost($id) {
        $params = $_POST;
        $file = File::find($id);
        $file->name = $params['filename'];
        $file->description = $params['description'];
        $validator = $file->validator();
        if ($validator->validate()) {
            $file->update();
            Redirect::to('/file/' . $file->id);
        } else {
            Redirect::to('/file' . $file->id . '/edit', $validator->errors());
        }
    }
    
    public static function destroyFile($id) {
        $file = File::find($id);
        unlink(FileController::$baseFilePath . $file->name);
        $file->destroy();
        Redirect::to('/filelist');
    }
}
