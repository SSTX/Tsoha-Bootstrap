<?php

class MessageController extends BaseController {

    public static function messageValidator($messageData) {
        $validator = new Valitron\Validator($messageData);
        $validator->rule('required', 'body')->label('Message body');
        return $validator;
    }

    public static function postMessage($fileId) {
        $params = $_POST;
        $messageData = array(
            'author' => self::get_user_logged_in(),
            'body' => $params['body'],
            'subject' => $params['subject'],
            'relatedFile' => File::find($fileId)
        );
        $validator = self::messageValidator($messageData);
        $message = new Message($messageData);
        if ($validator->validate()) {
            $message->save();
            Redirect::to('/file/' . $message->relatedFile->id);
        } else {
            $errors = self::array_flatten($validator->errors());
            View::make('file/viewFile.html', array(
                'errors' => $errors, 
                'file' => $message->relatedFile));
        }
    }

    public static function editMessage($id) {
        $params = $_POST;
        $message = Message::find($id);
        if (!self::logged_in($message->author)) {
            Redirect::to('/file/' . $message->relatedFile->id, array('err' => 'Login as the user who posted the message to edit it.'));
        }
        $messageData = array(
            'subject' => $params['subject'],
            'body' => $params['body']
        );
        $validator = self::messageValidator($messageData);
        if ($validator()->validate()) {
            $message->subject = $params['subject'];
            $message->body = $params['body'];
            $message->update();
            Redirect::to('/file/' . $message->relatedFile->id);
        } else {
            $errors = self::array_flatten($validator->errors());
            View::make('file/viewFile.html', array(
                'errors' => $errors,
                'file' => $message->relatedFile));
        }
    }

    public static function destroyMessage($id) {
        $message = Message::find($id);
        if (!self::logged_in($message->author)) {
            Redirect::to('/file/' . $message->relatedFile->id, array('err' => 'Login as the user who posted the message to edit it.'));
        }
        $message->destroy();
        Redirect::to('/file/' . $message->relatedFile->id);
    }

}
