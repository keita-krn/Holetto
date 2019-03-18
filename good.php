<?php
session_start();
require('functions.php');

$db = dbConnect();
//あるコメントに付いているいいねの数を取得する
if(isset($_POST['commentId'])){
    $c_id = $_POST['commentId'];

    try{
        //いいねを押したユーザーがそのコメントに既にいいねしていたかどうかを確認する
        $sql = 'SELECT * FROM good_table WHERE comment_id = ? AND user_id = ?';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $c_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $_SESSION['userId'], PDO::PARAM_INT);
        $stmt->execute();
        //select文によって返された行をカウントする
        $result = $stmt->rowCount();
        if(!empty($result)){
            //trueの場合データが既に存在する（＝既にいいねが押されている）ので削除する
            $sql = 'DELETE FROM good_table WHERE comment_id = ? AND user_id = ?';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(1, $c_id, PDO::PARAM_INT);
            $stmt->bindValue(2, $_SESSION['userId'], PDO::PARAM_INT);
            $stmt->execute();

            $sql = 'SELECT * FROM good_table WHERE comment_id = ?';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(1, $c_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll();
            echo count($result);
        }else{
            //falseの場合データが存在しない（＝いいねが押されていない）ので情報を格納する
            $sql = 'INSERT good_table SET comment_id=?,user_id=?,insert_date=now()';
            $stmt = $db->prepare($sql);
            $stmt->bindvalue(1, $c_id, PDO::PARAM_INT);
            $stmt->bindValue(2, $_SESSION['userId'], PDO::PARAM_INT);
            $stmt->execute();

            $sql = 'SELECT * FROM good_table WHERE comment_id = ?';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(1, $c_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll();
            echo count($result);
        } 
    }catch(Exception $e){
        $e->getMessage();
    }
    exit();
}