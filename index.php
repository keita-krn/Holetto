<?php 
session_start(); 
require_once("functions.php");
//カテゴリー情報を取得する（ページ下部に一覧表示する）
$categories = getCategories();

?>
<!DOCTYPE html>
<html lang=ja>
<head>
    <meta charset="UTF-8">
    <title>Holetto</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<!--ヘッダー部分-->
<?php require_once('header.php') ?>
<div class="welcome"><br><br>
    <?php if(isset($_SESSION['userName'])): ?>
        <h2>Find Your Favorite Categories!</h2>
            <p>自分のお気に入りの話題を、検索から、下の一覧から探そう。</p>
            <p>もしお好みの「カテゴリー」が無ければ、自分で作成することができます。</p>
            <a href="categoryCreate/index.php" class="categoryCreate welcome_btn">カテゴリーを作成する</a>
    <?php else: ?>
        <h2>Welcome to Holetto!</h2>
            <p>Holetto（オレット）は誰もが自由に好きな話題で語り合える</p>
            <p>スレッドフロート型総合掲示板です</p>
            <div class="welcome_btn_box">
                <a href="login.php" class="welcome_btn">ログイン</a>　　
                <a href="userCreate/index.php" class="welcome_btn">ユーザー登録</a>
            </div>
    <?php endif; ?>
</div>
<div class="main">
    <div class="categories">
        <?php foreach($categories as $category): ?>
            <a href="category.php?id=<?=$category['id']?>&page=1">
                <div class="category">
                    <img src="<?=h($category['category_image'])?>">
                    <p>
                        <?=cut(h($category['category_name']),12)?>
                    </p>
                </div>
            </a>
    <?php endforeach ?>
</div>

</div>
<!--フッター部分-->
<?php require_once('footer.php'); ?>