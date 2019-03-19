<?php
require_once('functions.php');

//受け取った検索ワードを変数に代入する
$keywords = $_GET['keyword'];
if(empty($keywords)){
    $errorMessage = '検索ワードを指定してください。';
}else{
    //全角スペースを半角スペースに変換
    $keywords = preg_replace('/　/', ' ', $keywords);
    //連続する半角スペースを1つの半角スペースに変換する
    $keywords = preg_replace('/\s+/', ' ', $keywords);
    //半角スペースで区切って配列化する
    $keywords = explode(' ', $keywords);

    //カテゴリーテーブルからの検索結果を取得する
    $categories = searchFromCategories($keywords);
    //スレッドテーブルからの検索結果を取得する
    $threads = searchFromThreads($keywords);
}
//検索結果に応じてメッセージを表示する
if(empty($categories) && empty($threads)){ 
    $message = '検索結果はありませんでした。'; 
}else if(empty($threads)){ 
    $message = 'スレッドの検索結果はありませんでした。'; 
}else if(empty($categories)){ 
    $message = 'カテゴリーの検索結果はありませんでした。'; 
}else{
    $message = '検索結果は以下の通りです。';
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Holetto</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" 
    integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
</head>
<body class="search_body">
    <!--ヘッダー部分-->
    <?php require_once('header.php') ?>
    <div class="keyword_box">
        <?php if(empty($errorMessage)): ?>
            <i class="fas fa-search fa-2x"></i>　検索ワード：
            <?php foreach($keywords as $keyword){ echo ' '.h($keyword); } ?><br><?=$message?>
        <?php else: ?>        
            <?=$errorMessage?>
        <?php endif; ?>
    </div>
    <!-----カテゴリーの検索結果表示部分----->
    <?php if(!empty($categories)): ?>
        <div class="categoriesByKeywords">
            <p><i class="fas fa-search fa-2x"></i>　カテゴリーの検索結果</p>
            <?php foreach($categories as $category): ?>
                <div class="categoryByKeywords">
                    <div class="categoryimageByKeywords">
                        <img src="<?$category['category_image']?>">
                    </div>
                    <div class="categoryinfoByKeywords">
                        <a href="category.php?id=<?=$category['id']?>&page=1">
                            <?=h($category['category_name'])?>
                        </a><br>
                        <span><?=h($category['category_introduce'])?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <!-----スレッドの検索結果表示部分----->
    <?php if(!empty($threads)): ?>
        <div class="threadsByKeywords">
            <p><i class="fas fa-search fa-2x"></i>　スレッドの検索結果</p>
            <?php foreach($threads as $thread): ?>
                <div class="threadByKeywords">
                    <div class="threadimageByKeywords">
                        <img src="<?$thread['thread_image']?>">
                    </div>
                    <div class="threadinfoByKeywords">
                        <a href="thread.php?id=<?=$thread['id']?>">
                            <?=h($thread['title'])?>
                        </a><br>
                        <span><?=h($thread['first_comment'])?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <!--フッター部分-->
    <?php require_once('footer.php'); ?>