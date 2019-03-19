<?php 
session_start();
require_once('functions.php');

//iframeタグは階層が違うファイルを参照できない為、参照する為の処理を行う。
if(!file_exists('image/bg-image/logo.png')){
    $f = '../';
}else{
    $f = '';
}
?>
<head>
    <meta charset="UTF-8">
    <title>Holetto</title>
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="header">
    <div class="top-logo">
        <a href="<?=$f?>index.php" id="toppage">
                <img src="<?=$f?>image/logo.png" class="logo">
        </a>
    </div>
    <div class="searchbox">
        <form action="<?=$f?>search.php" method="get">
            <div class="search_container">
                <input type="text" name="keyword" placeholder="カテゴリー・スレッドから検索" class="searchtext"/>
                <input type="submit" value="&#xf002"/>
            </div>
        </form>
    </div>
    <div class="menu">
        
            <!--ログイン時に表示する内容-->
            <?php if(!empty($_SESSION['userName'])): ?>
                <div class="menu_box">                                    
                    <img src="<?=$_SESSION['userImage']?>" 
                        alt="" class="user_image"/>                                                       
                    <a href="<?=$f?>mypage.php?id=<?=$_SESSION['userId']?>" class="user_name">
                        <?=h($_SESSION['userName'])?>
                        (ID:<?=$_SESSION['userId']?>)
                    </a>                                                                               
                    <a href="<?=$f?>mypage.php?id=<?=$_SESSION['userId']?>" class="btn">
                        マイページ
                    </a>                 
                    <a href="<?=$f?>logout.php" class="btn">
                        ログアウト
                    </a>                                     
                </div>
            <!--ログアウト時に表示する内容-->
            <?php else: ?>
                <div class="menu_box">
                    <a href="<?=$f?>login.php" class="login-btn btn">ログイン</a>
                    <a href="<?=$f?>userCreate/index.php" class="btn">ユーザー登録</a>
                </div>
            <?php endif; ?>
        
    </div>
</div>
