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
        $uploadError = null;
        $name = basename($_FILES['fileInput']['name']);
        if (!empty($_POST['nameOverride'])) {
            $name = $_POST['nameOverride'];
        }
        $size = $_FILES['fileInput']['size'];
        $desc = $_POST['fileDescription'];
        $path = '';
        $type = '';
        //if there is a file, set metadata based on it
        if (!empty($_FILES['fileInput']['tmp_name'])) {
            $ext = pathinfo($_FILES['fileInput']['name'], PATHINFO_EXTENSION);
            $path = 'files/' . md5_file($_FILES['fileInput']['tmp_name']) . '.' . $ext;
            $type = mime_content_type($_FILES['fileInput']['tmp_name']);
        }
        $finalPath = FileController::$basePath . $path;

        if (file_exists($finalPath)) {
            $uploadError = 'File already exists';
        } else if ($_FILES['fileInput']['error'] == UPLOAD_ERR_NO_FILE) {
            $uploadError = 'No file selected';
        }
        $file = new File(array(
            'name' => $name,
            'description' => $desc,
            'size' => $size,
            'path' => $path,
            'type' => $type,
            'author' => self::get_user_logged_in()
        ));
        $validator = $file->validator();
        if ($validator->validate() && !$uploadError) {
            move_uploaded_file($_FILES['fileInput']['tmp_name'], $finalPath);
            chmod($finalPath, 0744);
            $file->save();
            TagController::linkTags($file, $_POST['tags']); //this must be after file->save for file to have id
            Redirect::to('/file/' . $file->id, array('success' => 'File uploaded successfully.'));
        } else {
            Redirect::to('/upload', array('file' => $file, 'tags' => $_POST['tags'], 'errors' => $validator->errors(), 'err' => $uploadError));
        }
    }

    public static function editFileGet($id) {
        $file = File::find($id);
        if (self::get_user_logged_in() != $file->author) {
            Redirect::to('/file/' . $file->id, array('err' => 'Login as the uploader to edit files.'));
        }
        View::make('file/editFile.html', array('file' => $file));
    }

    public static function editFilePost($id) {
        $params = $_POST;
        $file = File::find($id);
        if (self::get_user_logged_in() != $file->author) {
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

    public static function searchFilesPost() {
        $terms = explode(' ', $_POST['search']);
        
    }
}
