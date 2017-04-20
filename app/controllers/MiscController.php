<?php

class MiscController extends BaseController {

    public static function index() {
        // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
        View::make('misc/home.html');
    }

    public static function searchPage() {
        View::make('misc/searchPage.html');
    }
}
