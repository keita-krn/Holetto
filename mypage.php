<?php 
session_start(); 
require_once('functions.php');

//idパラメータに値がない、または数字でない場合はトップ画面へ戻す
if(empty($_REQUEST['id']) || !is_numeric($_REQUEST['id'])){
    header('Location:index.php');
}else{
    $user_id = $_REQUEST['id'];
    //ユーザー情報を取得する
    $userInfo = getUserInfo($user_id);
    //投稿したコメントを10件取得する
    $comments = getCommentsByUserId($user_id, 10);
}
?>
<!DOCTYPE html>
<html lang=ja>
<head>
    <meta charset="UTF-8">
    <title>Holetto</title>
    <link rel="stylesheet" href="css/main.css">
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
</head>
<body class="mypage_body">
    <!--ヘッダー部分-->
    <?php require_once('header.php') ?>
    <div class="introduce">
        <div class="categoryimage">
            <img src="<?=$userInfo['user_image']?>">
        </div>
        <div class="categoryinfo">
            <table class="category_info_table">
            <tr>
                <td>
                    <span class="title">ユーザー名</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="categoryname"><?=h($userInfo['user_name'])?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="title">メールアドレス</span>
                </td>
            </tr>
            <tr>               
                <td>
                    <span class="categorysentence"><?=h($userInfo['email'])?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="title">登録日</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="categorysentence"><?=h($userInfo['insert_date'])?></span>
                </td>
            </tr>
            <?php if($_SESSION['userId'] == $user_id): ?>
                <tr>
                    <td>
                        <label class="update_user_image">プロフィール画像を変更する<input type="file" name="user_image" class="updateUserImage"></label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="delete.php?user_id=<?=$_SESSION['userId']?>" class="deleteAccount" 
                        onclick="return confirm('本当に削除します。よろしいですか？')">このアカウントを削除する</a>
                    </td>
                </tr>
            <?php endif; ?>
            </table>
        </div>
    </div>
    <!----------------------スレッド一覧表示部分---------------------->
    <div class="contents">
        
        <h3>コメント履歴(過去10件分表示)</h3>
        <?php if(empty($comments)): ?>
        <p>まだコメントしていない為表示できません。</p>
        <?php else: ?>
        <?php foreach($comments as $comment): ?>
            <div class="comment_box">
            <span><?=h($comment['comment'])?></span><br>
            <span class="user_comment">
                スレッド名：<a href="thread.php?id=<?=$comment['thread_id']?>"><?=h($comment['title'])?></a>
                 / カテゴリー：<a href="category.php?id=<?=$comment['category_id']?>&page=1"><?=h($comment['category_name'])?></a>
                / 投稿時間：<?=h($comment['insert_date'])?>
            </span>
            </div>
        <?php endforeach; ?>
        <?php endif; ?>
        
    </div>
    <!--フッター部分-->
    <?php require_once('footer.php'); ?>