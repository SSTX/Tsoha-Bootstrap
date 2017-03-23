<?php

class HelloWorldController extends BaseController {

    public static function index() {
        // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
        View::make('suunnitelmat/home.html');
    }

    public static function sandbox() {
        // Testaa koodiasi täällä
        echo 'Hello World!';
    }

    public static function filelist() {
        View::make('suunnitelmat/filelist.html');
    }

    public static function file() {
        View::make('suunnitelmat/file.html');
    }

    public static function upload() {
        View::make('suunnitelmat/upload.html');
    }

    public static function editFile($id) {
        View::make('suunnitelmat/editFile.html');
    }

    public static function editMessage($id) {
        View::make('suunnitelmat/editMessage.html');
    }

    public static function searchPage() {
        View::make('suunnitelmat/searchPage.html');
    }

    public static function viewTag($id) {
        View::make('suunnitelmat/viewTag.html')
    }
}
