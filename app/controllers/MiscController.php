<?php

class MiscController extends BaseController {

    public static function index() {
        View::make('misc/home.html');
    }

    public static function searchPage() {
        View::make('misc/searchPage.html');
    }
}
