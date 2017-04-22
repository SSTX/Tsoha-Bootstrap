<?php
class FileController extends BaseController {

    public static $basePath = '/home/ttiira/htdocs/';

    public static function fileValidator($fileData) {
        $validator = new Valitron\Validator($fileData);
        $validator->rule('required', 'name')->label('File name');
        $validator->rule('max', 'size', File::maxSize())
                ->message('{field} must be no more than ' . File::sizeConvert(File::maxSize()))
                ->label('File size');
        $validator->rule(function($field, $value, $params, $fields) {
            return !file_exists($value);
        }, 'finalPath')->message('File already exists');
        return $validator;
    }

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
        $fileData = array();
        if (!empty($_FILES['fileInput']['tmp_name'])) {
            if (empty($_POST['nameOverride'])) {
                $fileData['name'] = basename($_FILES['fileInput']['name']);
            } else {
                $fileData['name'] = $_POST['nameOverride'];
            }
            $ext = pathinfo($_FILES['fileInput']['name'], PATHINFO_EXTENSION);
            $fileData['path'] = 'files/' . md5_file($_FILES['fileInput']['tmp_name']) . '.' . $ext;
            $fileData['type'] = mime_content_type($_FILES['fileInput']['tmp_name']);
            $fileData['size'] = $_FILES['fileInput']['size'];
            $fileData['description'] = $_POST['fileDescription'];
            $fileData['author'] = self::get_user_logged_in();
        }
        $finalPath = FileController::$basePath . $fileData['path'];
        $fileData['finalPath'] = $finalPath;
        $validator = self::fileValidator($fileData);
        $file = new File($fileData);
        if ($validator->validate()) {
            move_uploaded_file($_FILES['fileInput']['tmp_name'], $finalPath);
            chmod($finalPath, 0744);
            $file->save();
            TagController::linkTags($file, $_POST['tags']); //this must be after file->save for file to have id
            Redirect::to('/file/' . $file->id, array('success' => 'File uploaded successfully.'));
        } else {
            $errors = self::array_flatten($validator->errors());
            Redirect::to('/upload', array('file' => $file, 'tags' => $_POST['tags'], 'errors' => $errors));
        }
    }

    public static function editFileGet($id) {
        $file = File::find($id);
        if (!self::logged_in($file->author)) {
            Redirect::to('/file/' . $file->id, array('err' => 'Login as the uploader to edit files.'));
        }
        View::make('file/editFile.html', array(
            'file' => $file,
            'tags' => Tag::linkedTags($file)));
    }

    public static function editFilePost($id) {
        $params = $_POST;
        $file = File::find($id);
        if (!self::logged_in($file->author)) {
            Redirect::to('/file/' . $file->id, array('err' => 'Login as the uploader to edit files.'));
        }
        $fileData = array(
            'name' => $params['filename'],
            'description' => $params['description']);
        $validator = self::fileValidator($fileData);
        if ($validator->validate()) {
            $file->name = $params['filename'];
            $file->description = $params['description'];
            $file->update();
            Redirect::to('/file/' . $file->id);
        } else {
            $errors = self::array_flatten($validator->errors());
            View::make('file/editFile.html', array(
                'file' => $file, 
                'errors' => $errors,
                'tags' => Tag::linkedTags($file)));
        }
    }

    public static function destroyFile($id) {
        $file = File::find($id);
        if (!self::logged_in($file->author)) {
            Redirect::to('/file/' . $file->id, array('err' => 'Login as the uploader to edit files.'));
        }
        unlink(FileController::$basePath . $file->path);
        $file->destroy();
        Redirect::to('/filelist');
    }

    public static function searchFilesPost() {
        $terms = array(
            'name' => $_POST['search']
        );
        $files = File::search($terms);
        Redirect::to('/filelist', array('files' => $files));
    }

}
