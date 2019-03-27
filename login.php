<?php
require_once('functions.php');
session_start();

if(!empty($_POST)){
    //入力チェックを行う
    $error['login'] = checkInput('ユーザー名またはパスワード',$_POST['login'],1,50);
    $error['password'] = checkInput('パスワード',$_POST['password'],5,30);
    
    $error = array_filter($error);   
    if(empty($error)){
        //入力された値を元にuser_tableからパスワードを取得する
        $result = getUserInfoToLogin($_POST['login']);
        //入力された値とハッシュ化したパスワードを比較する。一致した場合はtrueを返す⇒ログイン成功
        if(password_verify($_POST['password'], $result['password'])){ 
            session_regenerate_id(true);
            $_SESSION['userId'] = $result['id'];
            $_SESSION['userName'] = $result['user_name'];
            $_SESSION['userImage'] = $result['user_image'];
            $_SESSION['time'] = time();
            header('Location: index.php');
            exit();
        }else{
            $error['diff'] = "*ユーザー名、メールアドレスまたはパスワードが異なります。";        
        }
    }
}
?>
<!DOCTYPE html>
<html lang=ja>
<head>
    <meta charset="UTF-8">
    <title>Holetto</title>
    <link rel="stylesheet" href="css/form.css">
</head>
<body>
    <!--ヘッダー部分-->
    <?php require_once('header.php') ?>
    <div class="contains">
    <div class="image">
        <img src="image/meric-dagli-504235-unsplash.jpg">
    </div>
<div class="container">
<img src="image/logo.png">
<form action="" method="post">
    <div class="text">
        <label>ユーザー名またはメールアドレス</label>
        <span class="error"><?php if(!empty($error['login'])){ echo $error['login']; } ?></span>
        <input type="text" name="login" size="35" maxlength="50" value="<?=h($_POST['login'])?>"/>
    </div>
    <div class="text">
        <label>パスワード</label>
        <span class="error"><?php if(!empty($error['password'])){ echo $error['password']; } ?></span>
        <input type="password" name="password" size="35" maxlength="30" value="<?=h($_POST['password'])?>"/>
    </div>
    <div class="check">
        <span class="error"><?php if(!empty($error['diff'])){ echo $error['diff']; } ?></span>
        <!-- <input id="save" type="checkbox" name="save" value="on"><label for="save">次回から自動的にログインする</label> -->
    </div>
    <div class="sendbtn">
        <input type="submit" value="ログイン"/>
    </div>
    <div class="link">登録が済んでいない方は<a href="./userCreate">こちら</a><br>ホームへ戻りたい場合は<a href="index.php">こちら</a></div>
</form>
</div>
</div>
<?php require_once('footer.php') ?>