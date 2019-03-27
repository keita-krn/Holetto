<?php
session_start();
require('functions.php');
//コメントを削除する処理
if(!empty($_REQUEST['comment_id']) && !empty($_REQUEST['thread_id']) && is_numeric($_REQUEST['comment_id']) && is_numeric($_REQUEST['thread_id'])){
    $comment_id = $_REQUEST['comment_id'];
    $thread_id = $_REQUEST['thread_id'];

    //コメントを投稿したユーザーのIDと、ログイン中のユーザーのIDが一致すれば、コメントを削除する
    if(checkComment($comment_id, $_SESSION['userId'])){
        $image_url = getImagebyCommentId($user_id);
        deleteImageOnCloudinary($image_url);
        deleteComment($comment_id);
        header('Location: thread.php?id='.$thread_id);
        exit();
    }
//ユーザー情報を削除する処理
}else if(!empty($_REQUEST['user_id']) && is_numeric($_REQUEST['user_id']) && !empty($_SESSION['userId']) && $_REQUEST['user_id'] === $_SESSION['userId']){
    //ユーザー画像を削除する
    $image_url = getImagebyUserId($user_id);
    deleteImageOnCloudinary($image_url);
    deleteUser($user_id);
    header('Location: logout.php');
    exit();    
}else{
    header('Location: index.php');
    exit();
}