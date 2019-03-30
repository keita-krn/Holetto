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
if(empty($_FILES)){
    //トークン生成
    $token = bin2hex(random_bytes(32));
    $_SESSION['token'] = $token;
}
if(!empty($_FILES)){
    if(!hash_equals($_SESSION['token'], $_POST['token'])){
        header('Location: error.php');
        exit();
    }
    $message = checkImage($_FILES['user_image']);
    if(!empty($message)){
        $errorMessage = $message;
    }else{
        updateUserImage($_SESSION['userId'], $_FILES['user_image']);
        $_SESSION['userImage'] = getImagebyUserId($_SESSION['userId']);
    }
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
            <?php if($_SESSION['userId'] == $user_id): ?>
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
            <?php endif; ?>
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
            </table>
        </div>
        <?php if($_SESSION['userId'] == $user_id): ?>
            <div class="change_user_info_box">
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="token" value="<?php echo h($_SESSION['token'])?>">
                    <label class="update_user_image"><i class="fas fa-portrait"></i> プロフィール画像を変更する</label>
                    <span class="error"><?php if(!empty($errorMessage)){ echo $errorMessage; } ?></span>
                    <div class="update_user_image_box">
                        <input type="file" name="user_image">
                    </div>
                    <input type="submit" class="updateUserImage" value="変更する"/>
                </form>
                <div class="delete_user_info_box">
                    <a href="delete.php?user_id=<?=$_SESSION['userId']?>" onclick="return confirm('本当に削除します。よろしいですか？')">このアカウントを削除する</a>
                </div>
            </div>
        <?php endif; ?>
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