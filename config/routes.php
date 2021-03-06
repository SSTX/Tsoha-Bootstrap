<?php

$routes->get('/', function() {
    MiscController::index();
});

$routes->get('/search', function() {
    MiscController::searchPage();
});

$routes->get('/filelist', function() {
    FileController::filelist();
});

$routes->get('/file/:id', function($id) {
    FileController::viewFile($id);
});

// post: new message
$routes->post('/file/:id/', function($id) {
    MessageController::postMessage($id);
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

$routes->post('/message/:id/edit', function($id) {
    MessageController::editMessage($id);
});

$routes->get('/message/:id/edit', function($id) {
    MessageController::editPage($id);
});

$routes->get('/message/:id/destroy', function($id) {
    MessageController::destroyMessage($id);
});

$routes->get('/login', function() {
    UserController::loginGet();
});

$routes->post('/login', function() {
    UserController::loginPost();
});


$routes->get('/register', function() {
    UserController::registerGet();
});

$routes->post('/register', function() {
    UserController::registerPost();
});

$routes->get('/logout', function() {
    UserController::logoutGet();
});

$routes->get('/taglist', function() {
    TagController::tagList();
});

$routes->get('/tag/:id', function($id) {
    TagController::viewTag($id);
});

$routes->get('/user/:id', function($id) {
    UserController::userProfile($id);
});

$routes->get('/userlist', function() {
    UserController::userlist();
});

$routes->get('/user/:id/manage', function($id) {
    UserController::manageUser($id);
});

$routes->get('/user/:id/password', function($id) {
    UserController::passwordChangeGet($id);
});

$routes->post('/user/:id/password', function($id) {
    UserController::passwordChangePost($id);
});

$routes->get('/user/:id/destroy', function($id) {
    UserController::destroyUser($id);
});
