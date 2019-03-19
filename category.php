<?php 
session_start(); 
require('functions.php');

//idパラメータ、pageパラメータに値がない、または数字でない場合はトップ画面へ戻す
if(empty($_REQUEST['id']) || empty($_REQUEST['page']) || !is_numeric($_REQUEST['id']) || !is_numeric($_REQUEST['page'])){
    header('Location:index.php');
}else{
    $category_id = $_REQUEST['id'];
    $page = $_REQUEST['page'];
    //カテゴリーの詳細情報を取得する  
    $info = getCategoryInfo($category_id);
    
    //ページネーション処理を行うための準備をする
    //ある特定のカテゴリーに属するスレッドの個数を取得する
    $count = getThreadCount($category_id);

    //必要なページ数を求める（１ページ毎に10件表示する）
    $maxPage = ceil($count / 10); 

    //pageパラメータを元にSQL文のLIMITのスタート位置を求める
    $start = 10 * ($page - 1); 

    //カテゴリーIDと一致するスレッドの情報を10件取得する（そのスレッドについたコメントの投稿時間が新しい順に並べる）
    $threads = getThreadsByCategoryId($category_id, $start);

    //セッションに必要な情報を格納する（スレッド作成ページで使用）
    $_SESSION['categoryinfo'] = $info;
}
?>
<!DOCTYPE html>
<html lang=ja>
<head>
    <meta charset="UTF-8">
    <title>Holetto</title>
    <link rel="stylesheet" href="css/main.css">
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
</head>
<body class="category_body"> 
    <!--ヘッダー部分-->
    <?php require('header.php') ?>
    <div class="introduce">
        <div class="categoryimage">
            <img src="<?=h($info['category_image'])?>">
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
                        <span class="categoryname"><?=h($info['category_name'])?></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="title">紹介文</span>
                    </td>
                </tr>
                <tr>               
                    <td>
                        <span class="categorysentence"><?=h($info['category_introduce'])?></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="title">カテゴリーID</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="categorysentence"><?=h($info['category_id'])?></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="title">このカテゴリーを作ったユーザー：</span>
                    </td>
                </tr>
            </table>
            <table>
            <tr>            
                <?php if(empty($info['user_name'])): ?>
                <td>
                    <span class="categorysentence">※退会済みユーザー※</span>
                </td>
                <?php else: ?>
                <td>
                    <img src="<?=h($info['user_image'])?>" class="userimage">   
                </td>
                <td>
                    <span class="categorysentence"><?=h($info['user_name'])?></span>
                </td>
                <?php endif; ?>
            </tr>
            </table>
                <p>         
                    <!------スレッド作成時にログイン状態でない場合はログイン画面へ移動する------>
                    <i class="fas fa-pen-alt"></i>  
                    <?php if(empty($_SESSION['userName'])): ?>
                        <a href="login.php" class="newThread">
                    <?php else: ?>
                        <a href="threadCreate/index.php?id=<?=h($info['category_id'])?>" class="newThread">
                    <?php endif; ?>   
                    新規にスレッドを作成する
                    </a>
                </p>
        </div>
    </div>
    <!----------------------スレッド一覧表示部分---------------------->
    <div class="threads">
        <div class="thread-header">
            <?php if($count === 0): ?>
                <h2>まだこのカテゴリーにはスレッドがありません。</h2>
            <?php else: ?>
                <h2>スレッド一覧(<?=$count?>件)</h2>
            <?php endif; ?>
        </div>
        <?php foreach($threads as $thread): ?>
        <div class="thread">
            <div class="threadimage">
                    <img src="<?=h($thread['thread_image'])?>">    
            </div>
            <div class="threadinfo">
                <a href="thread.php?id=<?=$thread['thread_id'] ?>"><?=h($thread['title'])?></a><br>
                <span class="threaddate">
                    スレッド作成日時：<?=h($thread['created_thread_date'])?> 
                     / 最終コメント投稿日時：
                    <?php if(empty($thread['last_comment_date'])){echo 'コメントなし';}else{ echo $thread['last_comment_date'];}?>
                </span><br>
                <span class="threaduser"><i class="fas fa-user"></i> <?=h($thread['create_thread_user_name'])?></span>
            </div>
        </div>
        <?php endforeach; ?>
        <!--ページネーション部分-->
        <?php if($count != 0): ?>
        <div class="pagemenu">
            <?php if($page != 1): ?><a href="category.php?id=<?=$category_id?>&page=<?=$page - 1?>">前のページへ</a> ←<?php endif; ?>
            <?php for($i=1; $i<=$maxPage; $i++): ?>
                <?php if($page == $i): ?>
                    <?=$i?>
                <?php else: ?>
                    <a href="category.php?id=<?=$category_id?>&page=<?=$i?>"><?=$i?></a>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if($page != $maxPage): ?>→<a href="category.php?id=<?=$category_id?>&page=<?=$page + 1?>"> 次のページへ</a><?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    <!--フッター部分-->
    <?php require_once('footer.php'); ?>