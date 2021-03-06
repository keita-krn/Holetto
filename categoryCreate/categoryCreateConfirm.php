<?php
session_start();
require_once('../functions.php');

//カテゴリー登録画面からアクセスしていない場合はホーム画面に移動する
if(empty($_SESSION['categoryCreate'])){
    if(empty($_SESSION['userId'])){
        header('Location:../index.php');
        exit();
    }
}
if(empty($_POST)){
    //トークン生成
    $token = bin2hex(random_bytes(32));
    $_SESSION['token'] = $token;
}
//imageフォルダから画像を読み込む際の処理
if(mb_substr($_SESSION['categoryCreate']['categoryimage'], 0,5) === "image"){
    $c = "../";
}

 if(!empty($_POST)){
    if(!hash_equals($_SESSION['token'], $_POST['token'])){
        header('Location: ../error.php');
        exit();
    }
    //カテゴリー登録処理を行う
    $result = insertCategoryInfo($_SESSION['categoryCreate']['categoryname'],$_SESSION['categoryCreate']['categoryintroduce'],
    $_SESSION['categoryCreate']['categoryimage'],$_SESSION['userId']);
    if($result){
        //セッションの中身を削除する
        unset($_SESSION['categoryCreate']);
        header('Location:categoryCreateComplete.php');
        exit();
    }else{
        header('Location:categoryCreateConfirm.php');
        exit();
    }
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
            <form action="" method="post">
                <input type="hidden" name="token" value="<?php echo h($_SESSION['token'])?>">
                <p>記入した内容を確認して、「登録する」ボタンをクリックしてください。</p>
                <table>
                    <tr>
                        <td>カテゴリー名</td><td></td>
                        <td><?=h($_SESSION['categoryCreate']['categoryname'])?></td>
                    </tr>
                    <tr>
                        <td>紹介文</td><td></td>
                        <td ~~~ style="word-wrap:break-word;"><?=h($_SESSION['categoryCreate']['categoryintroduce'])?></td>
                    </tr>
                    <tr>
                        <td>サムネイル画像</td><td></td>
                    </tr>
                    <tr><td>
                    <img src="<?php if(!empty($c)){echo $c;}?><?=$_SESSION['categoryCreate']['categoryimage']?>" 
                    width="100" height="100" alt=""/></td><td></td>
                    </tr>
                </table>
                <div class="sendbtn">
                    <input type="submit" class="submit" value="登録する"/>
                    <div class="link">書き直したい場合は<a href="index.php?action=rewrite">こちら</a></div>
                </div>
            </form>
            
    </div>
</div>
    <!--フッター部分-->
    <?php require_once('../footer.php'); ?>