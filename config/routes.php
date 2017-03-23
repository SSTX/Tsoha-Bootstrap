<?php

$routes->get('/', function() {
    HelloWorldController::index();
});

$routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
});

$routes->get('/filelist', function() {
    HelloWorldController::filelist();
});

$routes->get('/file/:id', function($id) {
    HelloWorldController::file($id);
});

$routes->get('/upload', function() {
    HelloWorldController::upload();
});

$routes->get('/editfile/:id', function($id) {
    HelloWorldController::editFile($id);
});

$routes->get('/editmessage/:id', function($id) {
    HelloWorldController::editMessage($id);
});

$routes->get('/search', function() {
    HelloWorldController::searchPage();
});
