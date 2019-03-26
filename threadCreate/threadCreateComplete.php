<?php
session_start();
require_once('../functions.php');

if(empty($_SESSION['userId']) || empty($_SESSION['threadCreate'])){
    header('Location:../index.php');
    exit();
}
//作成されたスレッドのIDを取得する
$thread_id = getThreadIdByTitle($_SESSION['threadCreate']['threadtitle']);
unset($_SESSION['threadCreate']);

if($_SESSION['categoryinfo']['user_image'] === "image/user_noimage.jpeg"){
    $c = "../";
}else{
    $c = "";
}
?>
<!DOCTYPE html>
<html lang=ja>
<head>
    <meta charset="UTF-8">
    <title>Holetto</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" 
    integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
</head>
<body class="thread_body">
    <!--ヘッダー部分-->
    <?php require_once('../header.php') ?>   
    <div class="introduce">
    <div class="categoryimage">
            <img src="<?=h($_SESSION['categoryinfo']['category_image'])?>">
        </div>
        <div class="categoryinfo">
            <table class="category_info_table">
            <tr>
                <td>
                    <span class="title">カテゴリー</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="categoryname"><?=h($_SESSION['categoryinfo']['category_name'])?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="title">紹介文</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="categorysentence"><?=h($_SESSION['categoryinfo']['category_introduce'])?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="title">カテゴリーID</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="categorysentence"><?=h($_SESSION['categoryinfo']['category_id'])?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="title">このカテゴリーを作ったユーザー</span>
                </td>
            </tr>
            </table>
            <table>
            <tr>
                <td>
                    <span class="categorysentence"><?=h($_SESSION['categoryinfo']['user_name'])?></span>
                </td>
                <td>
                    <img src="<?=$c?><?=$_SESSION['categoryinfo']['user_image']?>" class="userimage">
                </td>
            </tr>
            </table>
        </div>
    </div>
    <div class="createthread">
                <p>新規スレッドを作成しました！</p>
        <p><i class="fas fa-undo-alt"></i> <a href="../thread.php?id=<?=$thread_id?>" class="threadcreatecomplete">作成したスレッドへ移動する</a></p>
    </div>
<!--フッター部分-->
<?php require_once('../footer.php'); ?>
