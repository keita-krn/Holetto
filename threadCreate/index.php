<?php 
session_start();
require_once('../functions.php');

//アカウント登録者でない、又はidが受け取れない場合はトップ画面へ戻す
if(empty($_SESSION['userId']) || empty($_REQUEST['id'])){
    header('Location:../index.php');
    exit();
}
if(empty($_POST)){
    //トークン生成
    $token = bin2hex(random_bytes(32));
    $_SESSION['token'] = $token;
}
if($_SESSION['categoryinfo']['user_image'] === "image/user_noimage.jpeg"){
    $c = "../";
}else{
    $c = "";
}
//スレッド作成処理を行う
if(!empty($_POST)){
    if(!hash_equals($_SESSION['token'], $_POST['token'])){
        header('Location: ../error.php');
        exit();
    }
    //エラー確認を行う
    $error['threadtitle'] = checkInput('タイトル',$_POST['threadtitle'],1,30);
    $error['firstcomment'] = checkInput('コメント',$_POST['firstcomment'],1,120);
    $error['threadimage'] = checkImage($_FILES['threadimage']);

    //全ての項目が埋まっている場合（入力内容確認画面へ）
    $check = array_filter($error);
    if(empty($check)){
        $_SESSION['threadCreate'] = $_POST;
        $_SESSION['threadCreate']['categoryid'] = $_REQUEST['id'];
        if($_FILES['threadimage']['name'] === ''){
            //画像が投稿されなかった場合はサムネイルにNo Image画像を採用する。
            $_SESSION['threadCreate']['threadimage'] = "image/noimage.png";
        }else{
            $_SESSION['threadCreate']['threadimage'] = uploadImageToCloudinary($_FILES['threadimage'],'thread');
        }
        header('Location:threadCreateConfirm.php');
        exit();
    }
}

//書き直し
if($_REQUEST['action'] == 'rewrite'){
$_POST = $_SESSION['threadCreate'];
$error['rewrite'] = true;
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
        <span class="makenewthread">新規スレッド作成</span>（*は必須項目です）
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="token" value="<?php echo h($_SESSION['token'])?>">
            <div>
                <label>スレッドタイトル*</label>
                <span class="error"><?php if(!empty($error['threadtitle'])){ echo $error['threadtitle']; } ?></span><br>
                <input type="text" name="threadtitle" size="35" maxlength="30" 
                   value="<?=h($_POST['threadtitle'])?>"/>
            </div>
            <div>
                <label>コメント*</label>
                <span class="error"><?php if(!empty($error['firstcomment'])){ echo $error['firstcomment']; } ?></span><br>
                <textarea name="firstcomment" cols="60" rows="5" maxlength="120" 
                      value="<?=h($_POST['firstcomment'])?>"></textarea>
            </div>
            <div>
                <label>アップする画像</label>
                <span class="error"><?php if(!empty($error['threadimage'])){ echo $error['threadimage']; } ?></span><br>
                <input type="file" name="threadimage" size="35">
            </div>
            <div>
                <input type="submit" class="submit" value="入力内容を確認する"/>
            </div>
            <span class="title">カテゴリー：<?=$_SESSION['categoryinfo']['category_name']?>/投稿者：<?=h($_SESSION['userName'])?></span>
        </form>
    </div>
    <!--フッター部分-->
    <?php require_once('../footer.php'); ?>