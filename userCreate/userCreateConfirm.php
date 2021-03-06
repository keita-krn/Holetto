<?php
session_start();
require_once('../functions.php');

//ユーザー登録画面からアクセスしていない場合はホーム画面に移動する
if(!isset($_SESSION['userCreate'])){
    header('Location:../index.php');
    exit();
}
if(empty($_POST)){
    //トークン生成
    $token = bin2hex(random_bytes(32));
    $_SESSION['token'] = $token;
}
//imageフォルダから画像を読み込む際の処理
if(mb_substr($_SESSION['userCreate']['image'], 0,5) === "image"){
    $c = "../";
}
if(!empty($_POST)){
    if(!hash_equals($_SESSION['token'], $_POST['token'])){
        header('Location: ../error.php');
        exit();
    }
    //ユーザー登録処理を行う
    insertUserInfo($_SESSION['userCreate']['username'],$_SESSION['userCreate']['email'],$_SESSION['userCreate']['password'],$_SESSION['userCreate']['image']);
    //セッションの中身を削除する
    unset($_SESSION['userCreate']);
    header('Location:userCreateComplete.php');
    exit();
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
            <img src="../image/nitish-meena-55274-unsplash.jpg">
        </div>
        <div class="container">
            <img src="../image/logo.png">
            <form action="" method="post">
                <input type="hidden" name="token" value="<?php echo h($_SESSION['token'])?>">
                <p>記入した内容を確認して、「登録する」ボタンをクリックしてください。</p>
                <table>
                    <tr>
                        <td>ユーザーネーム</td><td></td>
                        <td><?=h($_SESSION['userCreate']['username'])?></td>
                    </tr>
                    <tr>
                        <td>メールアドレス</td><td></td>
                        <td><?=h($_SESSION['userCreate']['email'])?></td>
                    </tr>
                    <tr>
                        <td>パスワード</td><td></td>
                        <td>※表示されません※</td>
                    </tr>
                    <tr>
                        <td>プロフィール画像</td><td></td>
                        <td><?php if($_SESSION['userCreate']['image'] === 'image/user_noimage.jpeg'): ?>（No Image画像）<?php endif; ?></td>
                    </tr>
                    <tr><td>
                    <img src="<?php if(!empty($c)){echo $c;}?><?=$_SESSION['userCreate']['image']?>" 
                    width="100" height="100" alt="" class="confirmimage"/></td><td></td>
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