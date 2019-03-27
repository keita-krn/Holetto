<?php
session_start();
require_once('functions.php');

//受け取った値が空、または数値でない場合はトップ画面に移す
if(empty($_REQUEST['id'] || !is_numeric($_REQUEST['id']))){
    header('Location:index.php');
}else{
    $thread_id = $_REQUEST['id'];
    //受け取ったスレッドのidを元にスレッド、カテゴリー、スレッドを作成したユーザーの情報を取得する
    $info = getThreadInfo($thread_id);
    //スレッドについたコメントを取得する
    $comments = getComments($thread_id);
}
if(!empty($_REQUEST['reply']) && is_numeric($_REQUEST['reply'])){
    $commentInfo = getCommentByCommentId($_REQUEST['reply']); 
}
//投稿フォーム処理
if(!empty($_POST)){
    //エラーチェック
    $error['comment'] = checkInput('コメント',$_POST['comment'],1,120);
    $error['commentimage'] = checkImage($_FILES['commentimage']);
 
    $check = array_filter($error);
    if(empty($check)){
        if($_FILES['commentimage']['name'] === ''){
            $commentImage = "noimage";
        }else{
            $commentImage = uploadImageToCloudinary($_FILES['commentimage'],"comment");
        }
        //comment_tableに情報を格納する
        if(empty($_POST['reply_comment_id']))
            $success = insertComment($_POST['comment'],$thread_id,$_SESSION['userId'],$commentImage,0);
        else{
            $success = insertComment($_POST['comment'],$thread_id,$_SESSION['userId'],$commentImage,$_POST['reply_comment_id']);
        }
        if($success){
            header('Location:thread.php?id='.$thread_id);
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang=ja>
<head>
    <meta charset="UTF-8">
    <title>Holetto</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" 
    crossorigin="anonymous">


</head>
<body class="thread_body">
    <!--ヘッダー部分-->
    <?php require_once('header.php') ?>
    <div class="thread_box">
        <div class="threadtop">
            <table>
            <tr>
                <td>
                    posted by
                </td>
                <?php if(empty($info['thread_creater'])): ?>
                <td>
                    <span class="threadCreater">退会済みユーザー</span>
                </td>
                <?php else: ?>
                <td>
                    <img src="<?=($info['user_image'])?>" class="createUserImage">
                </td>
                <td>
                    <a href="mypage.php?id=<?=$info['user_id']?>">
                        <span class="threadCreater"><?=h($info['thread_creater'])?></span>
                    </a>
                </td>
                <?php endif; ?>
                <td>
                    ：<?=$info['thread_create_date']?>
                </td>
            </tr>
        </table>
        <h2><?=h($info['title'])?></h2>
        <p><?=makeLink(nl2br(h($info['first_comment'])))?></p>
        <?php if($info['thread_image'] != "image/noimage.png"): ?>
            <img src="<?=$info['thread_image']?>" class="threadImage">
        <?php endif; ?>
        </div>
        <div class="comments">
        <?php if(empty($comments)): ?>
            <p>まだこのスレッドにはコメントがついていません。</p>
        <?php else: ?>
            <?php foreach($comments as $comment): ?>
                <div class="comment">
                    <table>
                        <tr>
                            <?php if(empty($comment['user_name'])): ?>
                                <td><span class="threadCreater">退会済みユーザー</span></td>
                            <?php else: ?>
                                <td><img src="<?=$comment['user_image']?>" class="createUserImage"></td>
                                <td>
                                    <a href="mypage.php?id=<?=$comment['user_id']?>">
                                        <span class="threadCreater"><?=h($comment['user_name'])?></span>
                                    </a>
                                </td>
                            <?php endif; ?>
                            <td>：<?=$comment['insert_date'] ?></td>
                            <td>
                                <?php if(!empty($_SESSION['userId'])): ?>
                                    <a href="thread.php?id=<?=$thread_id?>&reply=<?=$comment['comment_id']?>">
                                        <i class="fas fa-reply-all"></i><span class="reply">返信する</span>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <!--コメント削除ボタン部分（自身が書き込んだコメントは削除できる）-->
                            <?php if($comment['user_id'] === $_SESSION['userId']): ?>
                                <td>   
                                     <a href="delete.php?comment_id=<?=$comment['comment_id']?>&thread_id=<?=$thread_id?>" onclick="return confirm('本当に削除します。よろしいですか？')">                              
                                        <i class="fas fa-times active"></i><span class="delete">削除する</span>
                                    </a>                                  
                                </td>
                            <?php endif; ?>
                        </tr>
                    </table>
                    <tr>
                    <p><?=makeLink(nl2br(h($comment['comment'])))?></p>
                    <!--画像がアップされている場合は表示する-->
                    <?php if($comment['comment_image'] != 'noimage'): ?>
                        <img src="<?=($comment['comment_image'])?>" class="threadImage">
                    <?php endif; ?>

                    <!--------返信コメントの場合は返信先のコメントの内容を表示する-------->
                    <?php if($comment['reply_comment_id'] != 0): ?>
                    <div class="reply_comment_box">
                            <?php $reply_comment = getCommentByCommentId($comment['reply_comment_id']); ?>
                            <!--コメントが削除されていない場合-->
                            <?php if($reply_comment['user_name'] != ''): ?>
                            <i class="fas fa-quote-left"></i>
                                <table>
                                    <tr>
                                        <td><img src="<?=$reply_comment['user_image']?>" class="createUserImage"></td>
                                        <td>
                                            <a href="mypage.php?id=<?=$comment['user_id']?>">
                                                <span class="threadCreater"><?=h($reply_comment['user_name'])?></span>
                                            </a>
                                        </td>
                                        <td>：<?=$reply_comment['insert_date'] ?></td>
                                    </tr>
                                </table>
                                <p><?=makeLink(nl2br(h($reply_comment['comment'])))?></p>
                                <!--画像がアップされている場合は表示する-->
                                <?php if($reply_comment['comment_image'] != 'noimage'): ?>
                                    <img src="<?=$reply_comment['comment_image']?>" class="reply_threadImage">
                                <?php endif; ?>
                                <div class="quote"><i class="fas fa-quote-right"></i></div>
                            <!--コメントが削除されている場合-->
                            <?php else:?>
                                ※このコメントは削除されました※
                            <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <!-------------------ここまで返信先のコメント内容------------------->

                        <!----------いいねボタン部分---------->
                        <?php if(!empty($comment['user_name'])): ?>
                        <section class="cmmt" data-commentid="<?=$comment['comment_id']; ?>">
                            <div class="good-btn<?php if(isGoodExist($comment['comment_id'],$_SESSION['userId'])){ echo ' active'; }?>">
                                <i class="fa-heart fa-2x<?php if(isGoodExist($comment['comment_id'],$_SESSION['userId'])){ echo ' active fas';}else{ echo ' far';} ?>"></i>
                                <?php $num = getGoodCount($comment['comment_id']); ?>
                                <span><?=$num?></span>
                            </div>
                        </section>
                        <?php endif; ?>
                        <!-------いいねボタン部分ここまで------->
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        </div>
    </div>
    <div class="side_box">
        <div class="categoryinfointhread">
            <div class="categoryimage">
            <span>このスレッドが所属しているカテゴリー</span>
                <a href="category.php?id=<?=$info['category_id']?>&page=1">
                    <img src="<?=$info['category_image']?>">
                </a>
            </div>
            <div class="categoryinfo">
                <table class="category_info_table">
                <tr>
                    <td>
                        <span class="title">カテゴリー：</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="category.php?id=<?=$info['category_id']?>&page=1">
                        <span class="categoryname link"><?=h($info['category_name'])?></span>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="title">紹介文：</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="categorysentence"><?=h($info['category_introduce'])?></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="title">カテゴリーID：</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="categorysentence"><?=h($info['category_id'])?></span>
                    </td>
                </tr>
                </table>
            </div>
        </div>
            <div class="commentform">
            <?php if(!empty($_SESSION['userId'])): ?>
            <h4>投稿フォーム</h4>
                <span class="error"><?php if(!empty($error['comment'])){ echo $error['comment']; } ?></span><br>
                <span class="error"><?php if(!empty($error['commentimage'])){ echo $error['commentimage']; } ?></span>
                <form action="" method="post" enctype="multipart/form-data">
                    <?php if(!empty($commentInfo)): ?>
                        <div class="reply_user_name">
                            <?php if($commentInfo['user_id'] != $_SESSION['userId']): ?>
                                <?=h($commentInfo['user_name']) ?>さんに返信する
                            <?php else:?>
                                自身のコメントに返信する
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <label>コメント欄(120字まで)</label><br>
                        <textarea name="comment" cols="40" rows="6" maxlength="120" 
                                value="<?=h($_POST['comment'])?>"></textarea>
                    </div>
                    <br>
                    <div>
                        <label>画像をアップする</label><br>
                        <input type="file" name="commentimage" size="35">
                    </div>
                    <div>
                        <br>
                        <input type="submit" class="submit" value="投稿する"/>
                    </div>
                    <input type="hidden" name="reply_comment_id" value="<?=$_REQUEST['reply']?>"/>
                </form>
                <?php else: ?>
                    <a href="login.php">ログイン</a>することでスレッドにコメントを書き込むことができます。
                <?php endif; ?>
            </div>  
    </div>
    <!--フッター部分-->
    <?php require_once('footer.php'); ?>