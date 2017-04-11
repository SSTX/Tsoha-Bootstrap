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

$routes->post('/upload', function() {
    FileController::uploadPost();
});

$routes->get('/upload', function() {
    FileController::uploadGet();
});

$routes->get('/file/:id/edit', function($id) {
    FileController::editFileGet($id);
});

$routes->post('/file/:id/edit', function($id) {
    FileController::editFilePost($id);
});

$routes->get('/file/:id/destroy', function($id) {
    FileController::destroyFile($id);
});

$routes->get('/message/:id/edit', function($id) {
    HelloWorldController::editMessage($id);
});

$routes->get('/search', function() {
    HelloWorldController::searchPage();
});

$routes->get('/tag/:id', function($id) {
    HelloWorldController::viewTag($id);
});

$routes->get('/login', function() {
    UserController::loginGet();
});

$routes->post('/login', function() {
    UserController::loginPost();
});

$routes->post('/file/:id/postmessage', function($id) {
    MessageController::postMessage($id);
});
