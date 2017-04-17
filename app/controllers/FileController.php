<?php

class FileController extends BaseController {
    public static $basePath = '/home/ttiira/htdocs/';
    
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
        $uploadErrors = array();
        $name =  basename($_FILES['fileInput']['name']);
        $path = '';
        $type = '';
        if (!empty($_FILES['fileInput']['tmp_name'])) {
            $ext = pathinfo($_FILES['fileInput']['name'], PATHINFO_EXTENSION);
            $path = 'files/' . md5_file($_FILES['fileInput']['tmp_name']) . '.' . $ext;
            $type = mime_content_type($_FILES['fileInput']['tmp_name']);
        }
        $movepath = FileController::$basePath . $path;
        $size = $_FILES['fileInput']['size'];
        $desc = $_POST['fileDescription'];
        if (file_exists($movepath)) {
            $uploadErrors[] = 'File already exists';
        } else if ($_FILES['fileInput']['error'] == UPLOAD_ERR_NO_FILE) {
            $uploadErrors[] = 'No file selected';
        }
        $file = new File(array(
            'name' => $name,
            'description' => $desc,
            'size' => $size,
            'path' => $path,
            'type' => $type,
            'author' => $_SESSION['user']
        ));
        $validator = $file->validator();
        if ($validator->validate() && empty($uploadErrors)) {
            move_uploaded_file($_FILES['fileInput']['tmp_name'], $movepath);
            chmod($movepath, 0744);
            $file->save();
            Redirect::to('/file/' . $file->id);
        } else {
            $err = array_merge($validator->errors(), $uploadErrors);
            View::make('file/upload.html', array('file' => $file, 'errors' => $err));
        }
        
    }

    public static function editFileGet($id) {
        $file = File::find($id);
        View::make('file/editFile.html', array('file' => $file));
    }
    
    public static function editFilePost($id) {
        $params = $_POST;
        $file = File::find($id);
        if ($_SESSION['user'] != $file->author) {
            Redirect::to('/file/' . $file->id, array('err' => 'Login as the uploader to edit files.'));
        }
        $file->name = $params['filename'];
        $file->description = $params['description'];
        $validator = $file->validator();
        if ($validator->validate()) {
            $file->update();
            Redirect::to('/file/' . $file->id);
        } else {
            View::make('file/editFile.html', array('file' => $file, 'errors' => $validator->errors()));
        }
    }
    
    public static function destroyFile($id) {
        $file = File::find($id);
        if (self::get_user_logged_in() != $file->author) {
            Redirect::to('/file/' . $file->id, array('err' => 'Login as the uploader to edit files.'));
        }
        unlink(FileController::$basePath . $file->path);
        $file->destroy();
        Redirect::to('/filelist');
    }
}
