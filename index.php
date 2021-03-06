<?php
/*

 ____         ___    __              __           _       ______
/\  _`\   __ /\_ \  /\ \            /\ \__      /' \     /\  ___\
\ \,\L\_\/\_\\//\ \ \ \ \____    ___\ \ ,_\    /\_, \    \ \ \__/
 \/_\__ \\/\ \ \ \ \ \ \ '__`\  / __`\ \ \/    \/_/\ \    \ \___``\
   /\ \L\ \ \ \ \_\ \_\ \ \L\ \/\ \L\ \ \ \_      \ \ \  __\/\ \L\ \
   \ `\____\ \_\/\____\\ \_,__/\ \____/\ \__\      \ \_\/\_\\ \____/
    \/_____/\/_/\/____/ \/___/  \/___/  \/__/       \/_/\/_/ \/___/


 */
isset($_GET['token']) ? $token = 'bot' . $_GET['token'] : $token = false;
require 'config.php';
if ($config['connection_close']) {
    ignore_user_abort(true);
    header('Connection: close');
    flush();
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    }
}
require 'functions/update.php';
$bot = new botApi($token,$config);
$update = new update(file_get_contents('php://input'));
if (isset($update->message)) {
    $message = $update->message;
    if (isset($message->photo)) $photo = $message->photo;
    if (isset($message->audio)) $audio = $message->audio;
    if (isset($message->voice)) $voice = $message->voice;
    if (isset($message->animation)) $animation = $message->animation;
    if (isset($message->document)) $document = $message->document;
    if (isset($message->video)) $video = $message->video;
    if (isset($message->sticker)) $sticker = $message->sticker;
    if (isset($message->video_note)) $video_note = $message->video_note;
    if (isset($message->contact)) $contact = $message->contact;
    if (isset($message->location)) $location = $message->location;
    if (isset($message->venue)) $venue = $message->venue;
    if (isset($message->poll)) $poll = $message->poll;
}
if (isset($update->chat)) $chat = $update->chat;
if (isset($update->user)) $user = $update->user;
if (isset($update->callback)) $callback = $update->callback;
if (isset($update->inline_query)) $inline = $update->inline_query;
if (isset($update->callback)) $callback = $update->callback;

//Plugins
if ($config['plugins']['active']) {
    $startpls = array_diff(scandir('plugins/start'),['.', '..']);
    foreach ($startpls as $pl) {
        if (!in_array($pl,$config['plugins']['start_disabled'])) {
            include('plugins/start/' . $pl);
        }
    }
}

include 'commands.php';

if ($config['plugins']['active']) {
    $endpls = array_diff(scandir('plugins/end'),['.', '..']);
    foreach ($endpls as $pl) {
        if (!in_array($pl,$config['plugins']['end_disabled'])) {
            include('plugins/end/' . $pl);
        }
    }
}
