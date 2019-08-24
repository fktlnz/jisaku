<?php
//共通変数・関数ファイルを読込み
require('functions.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　Ajax　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// Ajax処理
//================================

// postがあり、ユーザーIDがあり、ログインしている場合
debug('$_POST["username_ajax"]:'.print_r($_POST["username_ajax"],true));
if(isset($_POST['username_ajax'])){
// if(isset($_POST['name']) && isset($_SESSION['user_id']) && isLogin()){
  debug('POST送信があります。');
  $name = $_POST['username_ajax'];
  debug('ユーザー名：'.$name);
  //例外処理
  try {
    // DBへ接続
    $dbh = Db::dbConnect();
    // レコードがあるか検索
    $sql = 'SELECT * FROM users WHERE username = :username';
    $data = array(':username' => $name);
    // クエリ実行
    $stmt = Db::queryPost($dbh, $sql, $data);
    $resultCount = $stmt->rowCount();
    // レコードが１件でもある場合
    if(!empty($resultCount)){
      // すでに指定したユーザー名が登録されていた
      debug('ユーザー名が重複していました。');
      echo 'true';
      
    }else{
      //　新規のユーザー名
      debug('新規のユーザー名です。');
      // FormCheck::$IsuserNameDup = false;
      echo 'false';
    }

  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}
debug('Ajax処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>