<?php
class MessageController extends BaseController {
    public static function postMessage($fileId) {
        $params = $_POST;
        $message = new Message(array(
            'body' => $params['body'],
            'subject' => $params['subject'],
            'relatedFile' => File::find($fileId)
        ));
        $validator = $message->validator();
        if ($validator->validate()) {
            $message->save();
            Redirect::to('/file/' . $message->relatedFile->id);
        } else {
            View::make('file/viewFile.html', array('errors' => $validator->errors(), 'file' => $message->relatedFile));
        }
    }

    public static function editMessage($id) {
        $params = $_POST;
        $message = Message::find($id);
        $message->subject = $params['subject'];
        $message->body = $params['body'];
        $validator = $message->validator();
        if ($validator()->validate()) {
            $message->update();
            Redirect::to('/file/' . $message->relatedFile->id);
        } else {
            View::make('file/viewFile.html', array('errors' => $validator->errors(), 'file' => $message->relatedFile));
        }
    }

    public static function destroyMessage($id) {
        $message = Message::find($id);
        $message->destroy();
        Redirect::to('/file/' . $message->relatedFile->id);
    }

}

