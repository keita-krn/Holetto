<?php
session_start();
require_once('../functions.php');

//アカウント登録者でない、又はidが受け取れない場合はトップ画面へ戻す
if(empty($_SESSION['userId'])){
    header('Location:../index.php');
    exit();
}
if($_SESSION['categoryinfo']['user_image'] === "image/user_noimage.jpeg"){
    $c = "../";
}else{
    $c = "";
}
//thread_tableに情報を格納する
if(!empty($_POST)){
    $insert_flag = insertThreadInfo($_SESSION['threadCreate']['threadtitle'],$_SESSION['threadCreate']['firstcomment'],
    $_SESSION['threadCreate']['threadimage'],$_SESSION['userId'],$_SESSION['threadCreate']['categoryid']);
    if($insert_flag){
        unset($_SESSION['threadCreate']);
        header('Location:threadCreateComplete.php');
        exit();
    }else{
        header('Location:threadCreateConfirm.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang=ja>
<head>
    <meta charset="UTF-8">
    <title>Holetto</title>
    <link rel="stylesheet" type="text/css" href="../css/main.css">
</head>
<body class="thread_body">
    <!--ヘッダー部分-->
    <?php require_once('../header.php') ?>    
    <div class="introduce">
            <div class="categoryimage">
                <img src="<?=$_SESSION['categoryinfo']['category_image']?>">
            </div>
            <div class="categoryinfo">
                <table class="category_info_table">
                <tr>
                    <td>
                        <span class="title">カテゴリー：</span>
                    </td>
                    <td>
                        <span class="categoryname"><?=h($_SESSION['categoryinfo']['category_name'])?></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="title">紹介文：</span>
                    </td>
                    <td>
                        <span class="categorysentence"><?=h($_SESSION['categoryinfo']['category_introduce'])?></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="title">カテゴリーID：</span>
                    </td>
                    <td>
                        <span class="categorysentence"><?=h($_SESSION['categoryinfo']['category_id'])?></span>
                    </td>
                </tr>
                </table>
                <table>
                <tr>
                    <td>
                        <span class="title">このカテゴリーを作ったユーザー：</span>
                    </td>
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
        <form action="" method="post">
            <input type="hidden" name="action" value="submit"/>
            <p>記入した内容を確認して、「作成する」ボタンをクリックしてください。</p>
                <table>
                    <tr>
                        <td>スレッドタイトル</td>
                        <td></td>
                        <td><?=h($_SESSION['threadCreate']['threadtitle'])?></td>
                    </tr>
                    <tr>
                        <td>コメント</td>
                        <td></td>
                        <td><?=h($_SESSION['threadCreate']['firstcomment'])?></td>
                    </tr>
                    <!--画像がアップされていない場合は表示しない-->
                    <?php if($_SESSION['threadCreate']['threadimage'] != "image/noimage.png"): ?>
                    <tr>
                        <td>アップする画像</td><td></td>
                    </tr>
                    <tr>
                        <td>
                            <img src="<?=$_SESSION['threadCreate']['threadimage']?>" alt="" class="confirm_thread_image"/>
                        </td>
                        <td></td>
                    </tr>
                    <?php endif; ?>
                </table>
            <div class="sendbtn">
            <input type="submit" class="submit" value="作成する"/>
            <div class="link">書き直したい場合は<a href="index.php?action=rewrite">こちら</a></div>
            </div>
        </form>
    </div>
    <!--フッター部分-->
    <?php require_once('../footer.php'); ?>