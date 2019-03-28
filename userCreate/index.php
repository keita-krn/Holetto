<?php
require('../functions.php');
session_start();

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
    $user_name = $_POST['username'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $pass2 = $_POST['password2'];
    $image = $_FILES['image'];

    //入力チェックを行い、エラー項目がある場合はエラーメッセージを取得する
    $error['username'] = checkInput('ユーザー名', $user_name, 1, 15);
    $error['email'] = checkInput('メールアドレス', $email, 1, 50);
    $error['password'] = checkInput('パスワード', $pass, 5, 30); 
    //確認用のパスワードをチェックする
    if($pass2 === ''){
        $error['password2'] = "*確認用のパスワードを入力してください。";
    }else if($pass != $pass2){
        $error['password2'] = "*１回目の入力と異なります。";
    }
    //画像チェック
    $error['image'] = checkImage($image);
    //メールアドレス形式チェック
    if(empty($error['email'])){
        $error['email'] = checkValidEmail($email);
    }
    //パスワード形式チェック
    if(empty($error['password'])){
        $error['password'] = checkValidPass($pass);
    }
    //入力されたユーザー名がすでに使われていないかチェックする
    if(empty($error['username'])){
        $error['username'] = checkExistUserName($user_name);
    }
    //入力されたメールアドレスがすでに登録されていないかチェックする
    if(empty($error['email'])){
        $error['email'] = checkExistUserEmail($email);
    }
    //全ての項目が埋まっている場合（確認画面へ）
    $error = array_filter($error);
    if(empty($error)){
        //セッションに情報を格納する。画像はリザイズ処理を行いアップロードする
        $_SESSION['userCreate'] = $_POST;
        //パスワードはハッシュ化して格納する
        $hash_pass = password_hash($_SESSION['userCreate']['password'], PASSWORD_DEFAULT);
        $_SESSION['userCreate']['password'] = $hash_pass;
        if($_FILES['image']['name'] === ''){
            $_SESSION['userCreate']['image'] = "image/user_noimage.jpeg";
        }else{
            $_SESSION['userCreate']['image'] = uploadImageToCloudinary($_FILES['image'],'user');
        }
        header('Location: userCreateConfirm.php');
        exit();
    }
}
//書き直しを行う場合の処理
if($_REQUEST['action'] == 'rewrite'){
$_POST = $_SESSION['userCreate'];
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
            <img src="../image/nitish-meena-55274-unsplash.jpg">
        </div>
        <div class="container">
            <img src="../image/logo.png">
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="token" value="<?php echo h($_SESSION['token'])?>">
                <div class="text">
                    <label>ユーザー名</label>
                    <span class="error"><?php if(!empty($error['username'])){ echo $error['username']; } ?></span>
                    <input type="text" name="username" size="35" maxlength="15"
                            value="<?=h($_POST['username'])?>"/>
                </div>
                <div class="text">
                    <label>メールアドレス</label>
                    <span class="error"><?php if(!empty($error['email'])){ echo $error['email']; } ?></span>
                    <input type="text" name="email" size="35" maxlength="50" 
                        value="<?=h($_POST['email'])?>"/>
                </div>
                <div class="text">
                    <label>パスワード(半角英数字をそれぞれ含む5~30文字)</label>
                    <span class="error"><?php if(!empty($error['password'])){ echo $error['password']; } ?></span>
                    <input type="password" name="password" size="10" maxlength="30" 
                        value="<?=h($_POST['password'])?>"/>
                </div>
                <div class="text">
                    <label>パスワード(確認用)</label>
                    <span class="error"><?php if(!empty($error['password2'])){ echo $error['password2']; } ?></span>
                    <input type="password" name="password2" size="10" maxlength="30" value=""/>
                </div>
                <div class="text">
                    <label>プロフィール画像</label>
                    <span class="error"><?php if(!empty($error['image'])){ echo $error['image']; } ?></span>
                    <?php if(!empty($error)): ?><span class="error">*恐れ入りますが、画像を改めて指定してください</span><?php endif; ?>
                    <div class="imagebtn">
                        <input type="file" name="image" size="35">
                    </div>
                </div>
                    <div class="sendbtn"><input type="submit" class="submit" value="入力内容を確認する"/></div>
                    <div class="link">ホームへ戻りたい場合は<a href="../index.php">こちら</a> / 既に登録済みの方は<a href="../login.php">こちら</a></div>
            </form>
        </div>
    </div>
<!--フッター部分-->
<?php require_once('../footer.php'); ?>