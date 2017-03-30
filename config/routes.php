<?php

$routes->get('/', function() {
    HelloWorldController::index();
});

$routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
});

$routes->get('/filelist', function() {
    FileController::filelist();
});

$routes->get('/file/:id', function($id) {
    FileController::viewFile($id);
});

$routes->get('/upload', function() {
    FileController::upload();
});

$routes->get('/editfile/:id', function($id) {
    FileController::editFile($id);
});

$routes->get('/editmessage/:id', function($id) {
    HelloWorldController::editMessage($id);
});

$routes->get('/search', function() {
    HelloWorldController::searchPage();
});

$routes->get('/tag/:id', function($id) {
    HelloWorldController::viewTag($id);
});

