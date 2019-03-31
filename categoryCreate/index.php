<?php
session_start();
require('../functions.php');

//登録者でなければカテゴリーを作成することはできないのでその場合はホーム画面へ戻す
if(!isset($_SESSION['userId'])){
    header('Location:../');
    exit();
}
if(empty($_POST)){
    //トークン生成
    $token = bin2hex(random_bytes(32));
    $_SESSION['token'] = $token;
}
if(!empty($_POST)){
    if(!hash_equals($_SESSION['token'], $_POST['token'])){
        header('Location: ../error.php');
        exit();
    }
    //エラー確認を行う
    $error['categoryname'] = checkInput('カテゴリー名',$_POST['categoryname'],1,15);
    $error['categoryintroduce'] = checkInput('紹介文',$_POST['categoryintroduce'],1,50);
    $error['categoryimage'] = checkImage($_FILES['categoryimage']);

    //重複するカテゴリー名がないかチェックをする
    if(!empty($_POST['categoryname'])){

        if(isExistCategoryName($_POST['categoryname'])){
            $error['categoryname'] = "*そのカテゴリー名は既に登録されています。";
        }
    }
    //全ての項目が埋まっている場合
    $check = array_filter($error);
    if(empty($check)){
        $_SESSION['categoryCreate'] = $_POST;
            //画像が投稿されなかった場合は用意した４つのサムネイルからランダムで１つ選ばれる
            if($_FILES['categoryimage']['name'] === ''){
                $_SESSION['categoryCreate']['categoryimage'] = "image/category_".rand(1,4).".jpg";
        }else{
            $_SESSION['categoryCreate']['categoryimage'] = uploadImageToCloudinary($_FILES['categoryimage'],'category');
        }
        header('Location: categoryCreateConfirm.php');
        exit();
    }
}
//書き直し
if($_REQUEST['action'] == 'rewrite'){
$_POST = $_SESSION['categoryCreate'];
$error['rewrite'] = true;
}
?>
<!DOCTYPE html>
<html lang=ja>
<head>
    <meta charset="UTF-8">
    <title>Holetto</title>
    <link rel="stylesheet" href="../css/form.css">
</head>
<body>
    <!--ヘッダー部分-->
    <?php require_once('../header.php') ?>
    <div class="contains">
        <div class="image">
            <img src="../image/tim-zankert-483708-unsplash.jpg">
        </div>
        <div class="container">
            <img src="../image/logo.png">
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="token" value="<?php echo h($_SESSION['token'])?>">
                <div class="text">
                    <label>カテゴリー名</label>
                    <span class="error"><?php if(!empty($error['categoryname'])){ echo $error['categoryname']; } ?></span>
                    <input type="text" name="categoryname" size="35" maxlength="15" 
                            value="<?=h($_POST['categoryname'])?>"/>
                </div>
                <div class="text">
                    <label>カテゴリーの紹介文</label>
                    <span class="error"><?php if(!empty($error['categoryintroduce'])){ echo $error['categoryintroduce']; } ?></span>
                    <textarea name="categoryintroduce" cols="35" rows="3" maxlength="50" 
                        value="<?=h($_POST['categoryintroduce'])?>"></textarea>
                </div>
                <div class="text">
                    <label>サムネイル画像</label>
                    <br>
                    <span class="error"><?php if(!empty($error['categoryimage'])){ echo $error['categoryimage']; } ?></span>
                    <div class="imagebtn">
                    <?php if(!empty($error)): ?><span class="error">*恐れ入りますが、画像を改めて指定してください</span><?php endif; ?>
                        <div class="update_image_box">
                            <input type="file" name="categoryimage" size="35">
                        </div>
                    </div>
                </div>
                    <div class="sendbtn"><input type="submit" class="submit" value="入力内容を確認する"/></div>
                    <div class="link">ホームへ戻りたい場合は<a href="../index.php">こちら</a></div>
            </form>
        </div>
    </div>
    <!--フッター部分-->
    <?php require_once('../footer.php'); ?>