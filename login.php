<?php
require_once('functions.php');
session_start();

if(!empty($_POST)){
    //入力チェックを行う
    $error['email'] = checkInput('メールアドレス',$_POST['email'],1,50);
    $error['password'] = checkInput('パスワード',$_POST['password'],1,30);   
    
        //入力された値を元にuser_tableから情報を取得する
        $result = getUserInfoByEmail($_POST['email']);
        //入力された値とハッシュ化した文字列を比較する。一致した場合はtrueを返す⇒ログイン成功
        if(password_verify($_POST['password'], $result['password'])){ 
            session_regenerate_id();
            $_SESSION['userId'] = $result['id'];
            $_SESSION['userName'] = $result['user_name'];
            $_SESSION['userImage'] = $result['user_image'];
            $_SESSION['time'] = time();
           
            header('Location: index.php');
            exit();
        }else{
            $error['login'] = "*メールアドレスまたはパスワードが間違っています。";        
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
        <img src="image/bg-image/meric-dagli-504235-unsplash.jpg">
    </div>
<div class="container">
<img src="image/bg-image/logo.png">
<form action="" method="post">
    <div class="text">
        <label>メールアドレス</label>
        <span class="error"><?php if(!empty($error['email'])){ echo $error['email']; } ?></span>
        <input type="text" name="email" size="35" maxlength="50" value="<?=h($_POST['email'])?>"/>
    </div>
    <div class="text">
        <label>パスワード</label>
        <span class="error"><?php if(!empty($error['password'])){ echo $error['password']; } ?></span>
        <input type="password" name="password" size="35" maxlength="30" value="<?=h($_POST['password'])?>"/>
    </div>
    <div class="check">
        <span class="error"><?php if(!empty($error['login'])){ echo $error['login']; } ?></span>
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