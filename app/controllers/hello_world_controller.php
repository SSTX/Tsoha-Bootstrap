<?php

  class HelloWorldController extends BaseController{

    public static function index(){
      // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
   	  View::make('home.html');
    }

    public static function sandbox(){
      // Testaa koodiasi täällä
      echo 'Hello World!';
    }
    
    public static function filelist() {
        View::make('filelist.html');
    }
    
    public static function file() {
        View::make('file.html');
    }
  }
