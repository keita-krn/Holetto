<?php
//HTMLタグの効果を打ち消す処理を行う。
function h($value){
    return htmlspecialchars($value,ENT_QUOTES,'UTF-8');
}
//文章が長い場合、文字数($num)を制限し、「…」を含めた$countの文字数にして返す
function cut($sentence, $num){
    if(mb_strlen($sentence) > $num){
        return mb_substr($sentence, 0, $num - 1)."…";
    }else{
        return $sentence;
    }
}
//URLリンク化
function makeLink($value){
    return mb_ereg_replace('(https?://[-_.!~*\'()a-zA-Z0-9;/?:@&=+$,%#]+)', '<a href="\1" target="_blank">\1</a>', $value);
}
//（連続する）スペースを1つの半角スペースに変換する
function ChangeSpacesIntoOneHalfWidthSpace($value){
    $value = preg_replace('/　/', ' ', $value);
    $value = preg_replace('/\s+/', ' ', $value);
    return $value;
}
//入力された内容をチェックする処理を行う
function checkInput($propertyName,$value,$minLength,$maxLength){
    $value = ChangeSpacesIntoOneHalfWidthSpace($value);
    if($value === '' || $value === ' '){
        $errorMessage = '*入力が行われていません。';
        return $errorMessage;
    }
    if(mb_strlen($value) < $minLength || mb_strlen($value) > $maxLength ){
        if($minLength != 1){
            $errorMessage = '*'.$propertyName.'は'.$minLength.'文字以上'.$maxLength.'文字以内で入力してください。';
        }else{
            $errorMessage = '*'.$propertyName.'は'.$maxLength.'文字以内で入力してください。';
        }
    }
    return $errorMessage;
}
function checkValidEmail($email){
    if(filter_var($email, FILTER_VALIDATE_EMAIL) === false){
        $errorMessage = '*正しい形式で入力してください。';
    }
    return $errorMessage;
}
//パスワードが半角英数字をそれぞれ1種類以上含む5文字以上30文字以下であるかどうかチェックする
function checkValidPass($pass){
    if(!preg_match('/\A(?=.*?[a-z])(?=.*?\d)[a-z\d]{5,30}+\z/i', $pass)){
        $errorMessage = '*正しい形式で入力してください。';
    }
    return $errorMessage;
}
//画像の拡張子が正しいか、サイズが大きすぎないかチェックする処理を行う。
function checkImage($image){
    $errorMessage = '';
    $fileName = $image['name'];
    $ext = substr($fileName, -4);

    //画像がアップされていない場合はエラーメッセージを出さない（No Image画像を表示する）
    if($ext === ''){
        return $errorMessage;
    }
    if($ext != '.jpg' && $ext != '.gif' && $ext != '.png' && $ext != 'jpeg'){
        $errorMessage = '*「.jpg」「.jpeg」「.png」「.gif」の画像を指定してください。';
    }
    if($image['size'] > 500000){
        $errorMessage = '*ファイルサイズが大きすぎます。';
    }
    return $errorMessage;
}

//Cloudinaryに画像をアップロードし、その画像ファイルのURLを返す。
function uploadImageToCloudinary($image, $key){
    require_once("vendor/autoload.php");
    
    $account = parse_url(getenv('CLOUDINARY_URL'));
    \Cloudinary::config(array(
        "cloud_name" => $account['host'],
        "api_key" => $account['user'],
        "api_secret" => $account['pass']
    )); 
    switch($key){
        case "thread":
        case "comment":
            $result = \Cloudinary\Uploader::upload($image['tmp_name']);
        break;
        default:
            $result = \Cloudinary\Uploader::upload($image['tmp_name'],array("width" => 300, "height" => 300));
        break;
    }
    return $result["secure_url"];    
}

/*-----------------------
DB接続用
 ------------------------*/
function dbConnect(){ 
    $url = parse_url(getenv("CLEARDB_DATABASE_URL"));
    $server = $url["host"];
    $user = $url["user"];
    $pass = $url["pass"];
    $dbname = substr($url["path"], 1);
    $db = new PDO('mysql:host=' . $server . ';dbname=' . $dbname . ';charset=utf8mb4',$user,$pass,array(\PDO::MYSQL_ATTR_INIT_COMMAND =>"SET time_zone = 'Asia/Tokyo'"));
    
    //例外をスローする
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //静的プレースホルダを使用する
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    //カラム名をキーとする連想配列で取得する
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $db;
}
/*-----------------------
  userCreateで使用する関数
 ------------------------*/
 function checkExistUserName($name){
    try{
        $db = dbConnect();
        $sql = 'SELECT COUNT(*) AS cnt FROM user_table WHERE user_name = ?';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $name, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetch();
        if($count['cnt'] > 0){
            return "*そのユーザー名は既に使用されています。";
        }
    }catch(PDOException $e){
        error_log('DB接続エラー：'.$e->getMessage());
        header('Location: error.php');
    }   
 }
 function checkExistUserEmail($email){
    try{
        $db = dbConnect();
        $sql = 'SELECT COUNT(*) AS cnt FROM user_table WHERE email = ?';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $email, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetch();
        if($count['cnt'] > 0){
            return "*そのメールアドレスは既に登録されています。";
        }
    }catch(PDOException $e){
        error_log('DB接続エラー：'.$e->getMessage());
        header('Location: error.php');
    }   
 }
 function insertUserInfo($name,$email,$password,$image){
     try{
        $db = dbConnect();
        $sql = 'INSERT INTO user_table SET user_name=?, email=?, password=?, user_image=?, insert_date=curdate()';
        $statement = $db->prepare($sql);
        $statement->bindValue(1,$name,PDO::PARAM_STR);
        $statement->bindValue(2,$email,PDO::PARAM_STR);
        $statement->bindValue(3,$password,PDO::PARAM_STR);
        $statement->bindValue(4,$image,PDO::PARAM_STR);
        $statement->execute();
    }catch(PDOException $e){
        error_log('DB接続エラー：'.$e->getMessage());
        header('Location: error.php');
    }  
 }
/*-----------------------
index.phpで使用する関数
 ------------------------*/
function getCategories(){
    try{
        $db = dbConnect();
        $sql = 'SELECT id,category_name,category_image FROM category_table ORDER BY insert_date DESC LIMIT 24';
        return $db->query($sql);
    }catch(PDOException $e){
        error_log('DB接続エラー：'.$e->getMessage());
        header('Location: error.php');
    }   
}
/*-----------------------
login.phpで使用する関数
 ------------------------*/
 //パスワード確認メソッド
function getUserInfoToLogin($value){
    try{
        $db = dbConnect();
        $sql = 'SELECT * FROM user_table WHERE user_name = ? OR email = ?';
        $statement = $db->prepare($sql);
        $statement->bindValue(1, $value, PDO::PARAM_STR);
        $statement->bindValue(2, $value, PDO::PARAM_STR);
        $statement->execute();
        return $statement->fetch();
    }catch(PDOException $e){
        error_log('DB接続エラー：'.$e->getMessage());
        header('Location: error.php');
    }   
}
/*-----------------------
category.phpで使用する関数
 ------------------------*/
 //カテゴリーの詳細情報を取得する 
function getCategoryInfo($category_id){
    try{
        $db = dbConnect();
        $sql = 'SELECT category_image,category_name,category_introduce,c.id as category_id,user_name,user_image 
                FROM category_table c
                LEFT JOIN user_table u
                ON c.create_category_user_id=u.id 
                WHERE c.id = ?';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1,$category_id,PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }catch(PDOException $e){
        error_log('DB接続エラー：'.$e->getMessage());
        header('Location: error.php');
    }
}
//ある特定のカテゴリーに属するスレッドの個数を取得する
function getThreadCount($category_id){
    try{
        $db = dbConnect();
        $stmt = $db->prepare('SELECT COUNT(*) AS count FROM thread_table WHERE category_id=?');
        $stmt->bindValue(1,$category_id,PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        $count = $result["count"];
        return $count;
    }catch(PDOException $e){
        error_log('DB接続エラー：'.$e->getMessage());
        header('Location: error.php');
    }
}
//カテゴリーIDと一致するスレッドの情報を10件取得する（そのスレッドについたコメントの投稿時間が新しい順に並べる）
function getThreadsByCategoryId($category_id, $start){
    try{
        $db = dbConnect();
        $sql = 'SELECT t.title,t.thread_image, t.id AS thread_id, t.insert_date AS created_thread_date, c.insert_date AS last_comment_date,
        u.user_name AS create_thread_user_name
        FROM thread_table t
        LEFT JOIN comment_table c
        ON t.id = c.thread_id
        LEFT JOIN user_table u
        ON t.create_thread_user_id = u.id
        WHERE t.category_id = ?
        ORDER BY c.insert_date DESC
        LIMIT ?,10';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $category_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $start, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }catch(PDOException $e){
        error_log('DB接続エラー：'.$e->getMessage());
        header('Location: error.php');
    }
}
/*-----------------------
  categoryCreateで使用する関数
 ------------------------*/
 function isExistCategoryName($category_name){
     try{
        $db = dbConnect();
        $sql = 'SELECT COUNT(*) AS cnt FROM category_table WHERE category_name = ?';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $category_name, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetch();
        if($count['cnt'] > 0){
            return true;
        }else{
            return false;
        }
    }catch(PDOException $e){
        error_log('DB接続エラー：'.$e->getMessage());
        header('Location: error.php');
    }
 }
 function insertCategoryInfo($name, $introduce, $image, $user_id){
     try{
        $db = dbConnect();
        $sql = 'INSERT INTO category_table SET category_name=?, category_introduce=?, 
        category_image=?, create_category_user_id=?, insert_date=curdate()';
        $statement = $db->prepare($sql);
        $statement->bindvalue(1,$name,PDO::PARAM_STR);
        $statement->bindValue(2,$introduce,PDO::PARAM_STR);
        $statement->bindValue(3,$image,PDO::PARAM_STR);
        $statement->bindValue(4,$user_id,PDO::PARAM_INT);
        $result = $statement->execute();
        return $result;
    }catch(PDOException $e){
        error_log('DB接続エラー：'.$e->getMessage());
        header('Location: error.php');
    }
 }
/*-----------------------
  thread.phpで使用する関数
 ------------------------*/

 //受け取ったスレッドのidを元にスレッド、カテゴリー、スレッドを作成したユーザーの情報を引き出す
 function getThreadInfo($thread_id){
     try{
        $db = dbConnect();
        $sql = 'SELECT 
        t.title,t.first_comment,t.thread_image,t.insert_date AS thread_create_date,c.id AS category_id,
        c.category_name,c.category_image,c.category_introduce,u.id AS user_id, u.user_name AS thread_creater,u.user_image
        FROM thread_table t
        LEFT JOIN category_table c 
        ON t.category_id = c.id 
        LEFT JOIN user_table u 
        ON t.create_thread_user_id = u.id
        WHERE t.id = ?';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1,$thread_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
     }catch(PDOException $e){
        error_log('DB接続エラー：'.$e->getMessage());
        header('Location: error.php');
     }
 }
//スレッドについたコメントを取得する
 function getComments($thread_id){
    try{
        $db = dbConnect();
        $sql = 'SELECT c.id AS comment_id,c.comment,c.user_id,c.comment_image,c.reply_comment_id,c.insert_date,u.id AS user_id,u.user_name,u.user_image
        FROM comment_table c 
        LEFT JOIN user_table u
        ON c.user_id = u.id
        WHERE c.thread_id=?';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1,$thread_id, PDO::PARAM_INT);
        $stmt->execute();
        return  $stmt->fetchAll(PDO::FETCH_ASSOC);
    }catch(PDOException $e){
        error_log('DB接続エラー：'.$e->getMessage());
        header('Location: error.php');
     }
 }
 //コメントIDから、コメント内容、コメント画像、コメント投稿時間、投稿者の名前と画像を取得する
 function getCommentByCommentId($comment_id){
     try{
         $db = dbConnect();
         $sql = 'SELECT c.comment,c.comment_image,c.insert_date,u.id AS user_id,u.user_name,u.user_image
         FROM comment_table c , user_table u
         WHERE c.user_id = u.id AND c.id = ?';
         $stmt = $db->prepare($sql);
         $stmt->bindValue(1,$comment_id, PDO::PARAM_INT);
         $stmt->execute();
         return $stmt->fetch(PDO::FETCH_ASSOC);
     }catch(PDOException $e){
        error_log('DB接続エラー：'.$e->getMessage());
        header('Location: error.php');
      }
 }
 //いいねボタン処理
function isGoodExist($comment_id,$user_id){
    //いいねを押したユーザーがそのコメントに既にいいねしていたかどうかを確認する
    try{
        $db = dbConnect();
        $sql = 'SELECT * FROM good_table WHERE comment_id = ? AND user_id = ?';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $comment_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->rowCount();
        if($count > 0){
            return true;
        }else{
            return false;
        }
    }catch(PDOException $e){
        error_log('DB接続エラー：'.$e->getMessage());
        header('Location: error.php');
     }
}
//あるコメントに付いているいいねの数を取得する
function getGoodCount($comment_id){
    try{
        $db = dbConnect();
        $sql = 'SELECT COUNT(*) AS cnt FROM good_table WHERE comment_id = ?';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $comment_id, PDO::PARAM_INT);
        $stmt->execute();
        $goodCount = $stmt->fetch();
        return $goodCount['cnt'];
    }catch(PDOException $e){
        error_log('DB接続エラー：'.$e->getMessage());
        header('Location: error.php');
    }
}
//コメントをcomment_tableに格納する
function insertComment($comment,$thread_id,$user_id,$image,$reply_comment_id){
    try{
        $db = dbConnect();
        $sql = 'INSERT INTO comment_table SET comment=?,thread_id=?,user_id=?,comment_image=?,reply_comment_id=?,insert_date=NOW()';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1,$comment,PDO::PARAM_STR);
        $stmt->bindValue(2,$thread_id,PDO::PARAM_INT);
        $stmt->bindValue(3,$user_id,PDO::PARAM_INT);
        $stmt->bindValue(4,$image,PDO::PARAM_STR);
        $stmt->bindValue(5,$reply_comment_id,PDO::PARAM_STR);
        $success = $stmt->execute();
        return $success;
    }catch(PDOException $e){
        error_log('DB接続エラー：'.$e->getMessage());
        header('Location: error.php');
     }
}
/*-----------------------
  threadCreateで使用する関数
 ------------------------*/
 function insertThreadInfo($title, $comment, $image, $user_id, $category_id){
     try{
         $db = dbConnect();
         $sql = 'INSERT INTO thread_table SET title=?,first_comment=?,thread_image=?,create_thread_user_id=?,category_id=?,insert_date=NOW()';
         $stmt = $db->prepare($sql);
         $stmt->bindValue(1,$title,PDO::PARAM_STR);
         $stmt->bindValue(2,$comment,PDO::PARAM_STR);
         $stmt->bindValue(3,$image,PDO::PARAM_STR);
         $stmt->bindValue(4,$user_id,PDO::PARAM_INT);
         $stmt->bindValue(5,$category_id,PDO::PARAM_INT);
         $insert_flag = $stmt->execute();
         return $insert_flag;
        }catch(PDOException $e){
            echo 'DB接続エラー：'.$e->getMessage();
         }
 }
 function getThreadIdByTitle($title){
    try{
        $db = dbConnect();
        $sql = 'SELECT id FROM thread_table WHERE title=? ORDER BY insert_date DESC LIMIT 0,1';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1,$title,PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['id'];
    }catch(PDOException $e){
        error_log('DB接続エラー：'.$e->getMessage());
        header('Location: error.php');
     }
 }
/*-----------------------
  search.phpで使用する関数
 ------------------------*/
 //カテゴリーテーブルから検索ワードに部分一致するカテゴリー名を検索する
 function searchFromCategories($keywords){
     try{
        $db = dbConnect(); 
        $sql = 'SELECT id, category_name, category_introduce, category_image FROM category_table WHERE';
        //SQL文を作成する
        for($i = 0; $i < count($keywords); $i++){
            if($i === 0){
                $sql .= " category_name LIKE ?"; 
            }else{
                $sql .= " or category_name LIKE ?";
            }
        }
        $stmt = $db->prepare($sql);
        for($i = 0; $i < count($keywords); $i++){
            //特殊文字のエスケープ処理を行う。
            $keyword = '%'.addcslashes($keywords[$i], '\_%').'%';
            $stmt->bindvalue($i + 1, $keyword, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
     }catch(PDOException $e){
        error_log('DB接続エラー：'.$e->getMessage());
        header('Location: error.php');
     }
 }
  //スレッドテーブルから検索ワードに部分一致するスレッドタイトルを検索する
 function searchFromThreads($keywords){
     try{
        $db = dbConnect();
        $sql = 'SELECT id, title, first_comment, thread_image FROM thread_table WHERE';
        //SQL文を作成する
        for($i = 0; $i < count($keywords); $i++){
            if($i === 0){
                $sql .= " title LIKE ?"; 
            }else{
                $sql .= " or title LIKE ?";
            }
        }
        $stmt = $db->prepare($sql);
        for($i = 0; $i < count($keywords); $i++){
            //特殊文字のエスケープ処理を行う。
            $keyword = '%'.addcslashes($keywords[$i], '\_%').'%';
            $stmt->bindValue($i + 1, $keyword, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
     }catch(PDOException $e){
        error_log('DB接続エラー：'.$e->getMessage());
        header('Location: error.php');
     }
 }
/*-----------------------
  mypage.phpで使用する関数
 ------------------------*/
 function getUserInfo($user_id){
    try{ 
        $db = dbConnect();
        $sql = 'SELECT user_name, email, user_image, insert_date FROM user_table WHERE id = ?';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }catch(PDOException $e){
        error_log('DB接続エラー：'.$e->getMessage());
        header('Location: error.php');
    }
 }

 //ユーザーが投稿したコメントを取得する
 function getCommentsByUserId($user_id,$count){
     try{
         $db = dbConnect();
         $sql = 'SELECT com.comment, com.insert_date, thr.title, thr.id AS thread_id, cat.category_name, cat.id AS category_id
         FROM comment_table com
         LEFT JOIN thread_table thr
         ON com.thread_id = thr.id
         LEFT JOIN category_table cat
         ON thr.category_id = cat.id
         WHERE com.user_id = ?
         ORDER BY com.insert_date DESC
         LIMIT 0 , ?';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $count,PDO::PARAM_INT);
        $stmt->execute();
        return  $stmt->fetchAll(PDO::FETCH_ASSOC);
    }catch(PDOException $e){
        error_log('DB接続エラー：'.$e->getMessage());
        header('Location: error.php');
     }
 }
 //ユーザー画像をアップデートする
function updateUserImage($user_id, $image){
    $up_image_url = uploadImageToCloudinary($image, "user");
    try{
        $db = dbConnect();
        $sql = 'UPDATE user_table SET user_image = ? WHERE id = ?';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $up_image_url, PDO::PARAM_STR);
        $stmt->bindValue(2, $user_id, PDO::PARAM_INT);
        $stmt->execute();
    }catch(PDOException $e){
        error_log('DB接続エラー：'.$e->getMessage());
        header('Location: error.php');
    }
}
 /*-----------------------
  delete.phpで使用する関数
 ------------------------*/
 function checkComment($comment_id, $user_id){
    try{
        $db = dbConnect();
        $sql = 'SELECT COUNT(*) AS count FROM comment_table WHERE id = ? AND user_id = ?';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $comment_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        if($result['count'] > 0){
            return true;
        }else{
            return false;
        }
    }catch(PDOException $e){
        error_log('DB接続エラー：'.$e->getMessage());
        header('Location: error.php');
    }
 }
 function deleteComment($comment_id){
     try{
         $db = dbConnect();
         $sql = 'DELETE FROM comment_table WHERE id = ?';
         $stmt = $db->prepare($sql);
         $stmt->bindValue(1, $comment_id, PDO::PARAM_INT);
         $stmt->execute();
    }catch(PDOException $e){
        error_log('DB接続エラー：'.$e->getMessage());
        header('Location: error.php');
    }
 }

 function deleteUser($user_id){
    if($user_id != 152){
        try{    
        $db = dbConnect();
        $sql = 'DELETE FROM user_table WHERE id = ?';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
        $stmt->execute();
        }catch(PDOException $e){
        echo 'DB接続エラー：'.$e->getMessage();        
        }
    }else{
        error_log('id:guest削除');
        header('Location: error.php');
    }
 }
 function getImagebyCommentId($comment_id){
    try{
        $db = dbConnect();
        $sql = 'SELECT comment_image FROM comment_table WHERE id = ?';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $comment_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['comment_image'];
   }catch(PDOException $e){
    error_log('DB接続エラー：'.$e->getMessage());
    header('Location: error.php');
   }
 }
 function getImagebyUserId($user_id){
    try{
        $db = dbConnect();
        $sql = 'SELECT user_image FROM user_table WHERE id = ?';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['user_image'];
   }catch(PDOException $e){
        error_log('DB接続エラー：'.$e->getMessage());
        header('Location: error.php');
   }
 }