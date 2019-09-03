<?php 

//================================
// ログ
//================================
//ログを取るか
ini_set('log_errors','on');
//ログの出力ファイルを指定
ini_set('error_log','php.log');
// display_errorsをONに設定
ini_set('display_errors', 0);
//セッションスタート
session_start();
//現在のセッションIDを差し替え（セキュリティ対策）
session_regenerate_id();

//================================
// デバッグ
//================================
//デバッグフラグ
$debug_flg = true;
//デバッグログ関数
function debug($str){
  global $debug_flg;
  if(!empty($debug_flg)){
    error_log('デバッグ：'.$str);
  }
}

//メッセージ変数
$img_msg = array();

//データベースから取得した情報を格納する(フォーム用)
$dbFormData = array();

//エラーメッセージ格納変数
$global_msg = array();

//================================
// セッション準備・セッション有効期限を延ばす
//================================
//セッションファイルの置き場を変更する（/var/tmp/以下に置くと30日は削除されない）
//session_save_path("/var/tmp/");
//ガーベージコレクションが削除するセッションの有効期限を設定（30日以上経っているものに対してだけ１００分の１の確率で削除）
ini_set('session.gc_maxlifetime', 60*60*24*30);
//ブラウザを閉じても削除されないようにクッキー自体の有効期限を延ばす
ini_set('session.cookie_lifetime ', 60*60*24*30);
//セッションを使う
session_start();
//現在のセッションIDを新しく生成したものと置き換える（なりすましのセキュリティ対策）
session_regenerate_id();

//================================
// 画面表示処理開始ログ吐き出し関数
//================================
function debugLogStart(){
  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示処理開始');
  debug('セッションID：'.session_id());
  debug('セッション変数の中身：'.print_r($_SESSION,true));
  debug('現在日時タイムスタンプ：'.time());
  if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
    debug( 'ログイン期限日時タイムスタンプ：'.( $_SESSION['login_date'] + $_SESSION['login_limit'] ) );
  }
}

//================================
// 画面に表示するメッセージを取得する関数
//一度取得するとセッションの内容を初期化する
//================================
function getDispMessage() {
    debug('メッセージ取得します：'.$_SESSION['msg']);
    if(!empty($_SESSION['msg'])){
        $msg_ = $_SESSION['msg'];
        $_SESSION['msg'] = '';
        return $msg_;
    }
}

//================================
// 画面に表示するメッセージをセット関数
//メッセージをセッション
//================================
function setDispMessage($msg) {
    debug('メッセージをセットします：'.$msg);
    $_SESSION['msg'] = $msg;
}


//================================
// HH:MM:SSの時間をmsec秒に変換する関数
// $time: HH:MM:SS形式の時間
//================================
function getMsecTime($time) {
    debug('HH:MM:SS⇒msecに変換します');
    $subject_time = explode(":", $time); 
    $subject_time_ms = $subject_time[0] * 3600000 + $subject_time[1] * 60000 +$subject_time[2] * 1000;
    return $subject_time_ms;
}

//================================
// msec秒を[HH,MM,SS]形式に変換する関数
// $time: msec秒
//================================
function getHMSTime($time_ms) {

    //h(時)
    $h = floor($time_ms / 3600000);            
    //m(分) = 135200 / 60000ミリ秒で割った数の商　-> 2分

    //1時間（60分）を超えていた場合に以下の計算では60分未満となるようにする
    //今日の勉強時間で表示される時間はx分x秒であり、2時間の場合も分表示120分であるため
    $time_ms = $time_ms % 3600000;
    $m = floor($time_ms / 60000);
    //s(秒) = 135200 / 1000ミリ秒
    $s = floor($time_ms % 60000 / 1000);

    //HTML 上で表示の際の桁数を固定する　例）3 => 03　、 12 -> 12
    //javascriptでは文字列数列を連結すると文字列になる
    //文字列の末尾2桁を表示したいのでsliceで負の値(-2)引数で渡してやる。
    $h = substr(('0'.strval($h)),-2); 
    $m = substr(('0'.strval($m)),-2); 
    $s = substr(('0'.strval($s)),-2);

    //データベース格納用
    $time_HMS[]=$h;
    $time_HMS[]=$m;
    $time_HMS[]=$s;

    return $time_HMS;
}

/**
 * 画像をサーバーにアップロードし、画像格納パスを返す
 *
 * @param $files $_FILES['pic']
 * @return 画像格納パス
 */

 function upLoadImg($files) {
    debug('画像をサーバーにアップロードします');
    debug('$_FILES情報：'.print_r($files, true));

    try {

        if(isset($files['error']) && is_int($files['error'])){

            switch($files['error']){
                case UPLOAD_ERR_OK: // OK
                    break;
                case UPLOAD_ERR_NO_FILE:   // ファイル未選択の場合
                    throw new RuntimeException('ファイルが選択されていません');
                case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズが超過した場合
                case UPLOAD_ERR_FORM_SIZE: // フォーム定義の最大サイズ超過した場合
                    throw new RuntimeException('ファイルサイズが大きすぎます');
                default: // その他の場合
                    throw new RuntimeException('その他のエラーが発生しました');
            }

            $type = @exif_imagetype($files['tmp_name']);
            debug('$type: '.print_r($type, true));
            if(!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)){
                throw new RuntimeException('画像が未対応です');
            }

            $path = 'uploads/'.sha1_file($files['tmp_name']).image_type_to_extension($type);
            if(!move_uploaded_file($files['tmp_name'], $path)){
                throw new RuntimeException('画像の保存に失敗しました');
            }

            // 保存したファイルパスのパーミッション（権限）を変更する
            chmod($path, 0644);  
            debug('画像のアップロードに成功しました');
            
            return $path;
    
        }


    }catch(RuntimeException $e){
        debug('RuntimeExceptionエラー：'.$e->getMessage());
        global $global_msg;
        $global_msg['img'] = $e->getMessage();
    }
 }

 // サニタイズ
function sanitize($str){
  return htmlspecialchars($str,ENT_QUOTES);
}

/**
 * フォームの値を取得する
 *
 * @param $files $_FILES['pic']
 * @return 画像格納パス
 */
function getUserForm($str, $IsGet=false) {
    $method = $_POST;
    if($IsGet) $method = $_GET;

    global $dbFormData;
    global $global_msg;

    if(!empty($dbFormData[$str])){//データベースにデータある場合
        //フォームのエラーがある場合
        if(!empty($global_msg[$str])){
            //POSTにデータがある場合
            if(isset($method[$str])){
                return sanitize($method[$str]);
            }else{
                //ない場合（基本ありえない）はDBの情報を表示
                return sanitize($dbFormData[$str]);
            }
        }else{
            //POSTにデータがあり、DBの情報と違う場合
            if(isset($method[$str]) && $method[$str] !== $dbFormData[$str]){
                return sanitize($method[$str]);
            }else{
                return sanitize($dbFormData[$str]);
            }
        }

    }else{//データベースにデータがない
        if(isset($method[$str])){
            return sanitize($method[$str]);
        }

    }
}



class Messages {
    const MSG01 = '入力必須です';
    const MSG02 = 'Emailの形式で入力してください';
    const MSG03 = 'パスワード（再入力）が合っていません';
    const MSG04 = '半角英数字のみご利用いただけます';
    const MSG05 = '文字以上で入力してください';
    const MSG06 = '56文字以内で入力してください';
    const MSG07 = 'エラーが発生しました。しばらく経ってからやり直してください。';
    const MSG08 = 'このニックネームは使用できません';
    const MSG09 = 'メールアドレスまたはパスワードが違います';
    const MSG10 = '電話番号の形式が違います';
    const MSG11 = '郵便番号の形式が違います';
    const MSG12 = '古いパスワードが違います';
    const MSG13 = '古いパスワードと同じです';
    const MSG14 = '文字で入力してください';
    const MSG15 = '正しくありません';
    const MSG16 = '有効期限が切れています';
    const MSG17 = '半角数字のみご利用いただけます';
    const MSG18 = '画像の登録に失敗しました';
    const SUC01 = 'パスワードを変更しました';
    const SUC02 = 'プロフィールを変更しました';
    const SUC03 = 'メールを送信しました';
    const SUC04 = '登録しました';
    const SUC05 = '購入しました！相手と連絡を取りましょう！';
}


class FormCheck {
    //================================
    // バリデーション関数
    //================================
    public static $IsuserNameDup;

    //エラー格納用
    public $err_msg;

    //Emailフォームチェック
    public function CheckFormName($str, $IsSignUp){
        global $global_msg;
        //未入力チェック
        $this->validRequired($str, 'name');
        if(empty($global_msg)){
            //最大文字数チェック
            $this->validMaxLen($str,'name');
            //バリデーションチェック
            //validEmail($str, 'name');
            if(empty($global_msg) && $IsSignUp){
                //Email重複チェック
                $this->validUsernameDup($str, 'name');
            }
        }
    }

    //パスワードフォームチェック
    public function CheckFormPassword($str){
        //半角チェック
        $this->validHalf($str, 'pass');
        //パスワードの最大文字数チェック
        $this->validMaxLen($str, 'pass');
        //パスワードの最小文字数チェック
        $this->validMinLen($str, 'pass');
    }

    //バリデーション関数（未入力チェック）
    public function validRequired($str, $key){
        global $global_msg;
        if($str === ''){ //金額フォームなどを考えると数値の０はOKにし、空文字はダメにする        
            $global_msg[$key] = Messages::MSG01;
        }
    }
    //バリデーション関数（Email形式チェック）
    public function validEmail($str, $key){
        global $global_msg;
        if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){        
            $global_msg[$key] = Messages::MSG02;
        }
    }
    //バリデーション関数（Email重複チェック）
    public function validUsernameDup($name, $str){
        global $global_msg;
        //例外処理
        try {
            // DBへ接続
            $dbh = Db::dbConnect();
            // SQL文作成
            $sql = 'SELECT count(*) FROM users WHERE username = :username AND delete_flg = 0';
            $data = array(':username' => $name);
            // クエリ実行
            $stmt = Db::queryPost($dbh, $sql, $data);
            // クエリ結果の値を取得
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            //array_shift関数は配列の先頭を取り出す関数です。クエリ結果は配列形式で入っているので、array_shiftで1つ目だけ取り出して判定します
            if(!empty(array_shift($result))){
            $global_msg[$str] = Messages::MSG08;
            }
        } catch (Exception $e) {
            error_log('エラー発生:' . $e->getMessage());
            $global_msg['common'] = Messages::MSG07;
        }
    }
    //バリデーション関数（同値チェック）
    function validMatch($str1, $str2, $key){
        global $global_msg;
        if($str1 !== $str2){
            $global_msg[$key] = Messages::MSG03;
        }
    }
    //バリデーション関数（最小文字数チェック）
    function validMinLen($str, $key, $min = 6){
        global $global_msg;
        if(mb_strlen($str) < $min){
            $global_msg[$key] = $min . Messages::MSG05;
        }
    }
    //バリデーション関数（最大文字数チェック）
    function validMaxLen($str, $key, $max = 256){
        global $global_msg;
        if(mb_strlen($str) > $max){
            $global_msg[$key] = Messages::MSG06;
        }
    }
    //バリデーション関数（半角チェック）
    function validHalf($str, $key){
        global $global_msg;
        if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
            $global_msg[$key] = Messages::MSG04;
        }
    }
    //電話番号形式チェック
    function validTel($str, $key){
        global $global_msg;
        if(!preg_match("/0\d{1,4}\d{1,4}\d{4}/", $str)){
            $global_msg[$key] = Messages::MSG10;
        }
    }
    //郵便番号形式チェック
    function validZip($str, $key){
        global $global_msg;
        if(!preg_match("/^\d{7}$/", $str)){
            $global_msg[$key] = Messages::MSG11;
        }
        }
    //半角数字チェック
    function validNumber($str, $key){
        global $global_msg;
        if(!preg_match("/^[0-9]+$/", $str)){
            $global_msg[$key] = Messages::MSG17;
        }
    }
    //固定長チェック
    function validLength($str, $key, $len = 8){
        global $global_msg;
        if( mb_strlen($str) !== $len ){
            $global_msg[$key] = $len . Messages::MSG14;
        }
    }
    //パスワードチェック
    function validPass($str, $key){
    //半角英数字チェック
    validHalf($str, $key);
    //最大文字数チェック
    validMaxLen($str, $key);
    //最小文字数チェック
    validMinLen($str, $key);
    }
    //selectboxチェック
    function validSelect($str, $key){
        global $global_msg;
        if(!preg_match("/^[0-9]+$/", $str)){
            $global_msg[$key] = Messages::MSG15;
        }
    }
}

class Db {

    //================================
    // データベース
    //================================
    //DB接続関数
    static public function dbConnect(){
    //DBへの接続準備
    $dsn = 'mysql:dbname=myapp;host=localhost;charset=utf8';
    $user = 'root';
    $password = 'root';
    $options = array(
        // SQL実行失敗時にはエラーコードのみ設定
        PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
        // デフォルトフェッチモードを連想配列形式に設定
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // バッファードクエリを使う(一度に結果セットをすべて取得し、サーバー負荷を軽減)
        // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );
    // PDOオブジェクト生成（DBへ接続）
    $dbh = new PDO($dsn, $user, $password, $options);
    return $dbh;
    }
    //SQL実行関数
    //function queryPost($dbh, $sql, $data){
    //  //クエリー作成
    //  $stmt = $dbh->prepare($sql);
    //  //プレースホルダに値をセットし、SQL文を実行
    //  $stmt->execute($data);
    //  return $stmt;
    //}
    static public function queryPost($dbh, $sql, $data){
    //クエリー作成
    $stmt = $dbh->prepare($sql);
    //プレースホルダに値をセットし、SQL文を実行
    if(!$stmt->execute($data)){
        global $global_msg;
        debug('クエリに失敗しました。');
        debug('失敗したSQL：'.print_r($stmt,true));
        $global_msg['common'] = Messages::MSG07;
        return 0;
    }
    debug('クエリ成功。');
    return $stmt;
    }


    static public function getAllSubject($user_id) {
        debug('勉強項目をすべて取得します');
        try {
            $dbh = Db::dbConnect();
            $sql = 'SELECT id, name, time FROM subject WHERE user_id =:u_id AND delete_flg=0';
            $data = array(':u_id' => $user_id);

            $stmt = Db::queryPost($dbh, $sql, $data);
            
            if($stmt){
                debug('クエリに成功しました');
                return $stmt->fetchAll();
            }else {
                debug('クエリ失敗しました');
                debug('失敗したクエリ：'.$sql);
                
            }



        }catch(Exception $e) {
            debug('勉強項目の取得に失敗しました');
            debug($e->getMessages());
        }
    }

    static public function getSubject($subject_id, $user_id) {
        debug('勉強項目を取得します');

        try {
            $dbh = Db::dbConnect();
            $sql = 'SELECT name, time FROM subject WHERE id=:s_id AND user_id=:u_id AND delete_flg=0';
            $data = array(':s_id' => $subject_id, ':u_id' => $user_id);
        
            $stmt = Db::queryPost($dbh, $sql, $data);

            if($stmt) {
                debug('項目の取得に成功しました');
                return $stmt->fetch(PDO::FETCH_ASSOC);

            }else{
                debug('項目の取得に失敗に失敗しました');
            }
        }catch(Exception $e){
            debug('クエリに失敗しました');
            debug('失敗したクエリ：'.$e->getMessages());
        }
    }

    static public function addSubject($subject, $user_id) {
        debug('勉強項目を追加します');

        try {
            $dbh = Db::dbConnect();
            $sql = 'INSERT INTO subject (name, user_id, create_date) VALUES (:name, :u_id, :c_date) ';
            $data = array(':name' => $subject, ':u_id' => $user_id, ':c_date' => date('Y-m-d H:i:s'));
        
            $stmt = Db::queryPost($dbh, $sql, $data);

            if($stmt) {
                debug('項目の追加に成功しました');
                return true;

            }else{
                debug('項目の追加に失敗しました');
                return false;
            }
        }catch(Exception $e){
            debug('クエリに失敗しました');
            debug('失敗したクエリ：'.$e->getMessages());
            return false;
        }
    }

    //勉強した時間をデータベースに保存する
    static public function setStudyTime($subject_id, $user_id, $study_time) {
        debug('勉強時間を更新します');

        try {
            $dbh = Db::dbConnect();
            $sql = 'UPDATE subject SET `time`=:st_time WHERE id =:p_id AND user_id=:u_id';
            $data = array(':st_time' => $study_time, ':p_id' => $subject_id, ':u_id' => $user_id );
        
            $stmt = Db::queryPost($dbh, $sql, $data);

            if($stmt) {
                debug('勉強時間を更新しました');
                return true;

            }else{
                debug('勉強時間を更新できませんでした');
                return false;
            }

        }catch(Exception $e){
            debug('クエリに失敗しました');
            debug('失敗したクエリ：'.$e->getMessages());
            return false;
        }
    }

    //勉強項目を削除する
    static public function deleteSubject($subject_id) {
        debug('勉強項目を削除します');

        try {
            $dbh = Db::dbConnect();
            $sql = 'UPDATE subject SET delete_flg=1 WHERE id =:p_id';
            $data = array(':p_id' => $subject_id);
        
            $stmt = Db::queryPost($dbh, $sql, $data);

            if($stmt) {
                debug('項目の削除に成功しました');
                return true;

            }else{
                debug('項目の削除に失敗しました');
                return false;
            }
        }catch(Exception $e){
            debug('クエリに失敗しました');
            debug('失敗したクエリ：'.$e->getMessages());
            return false;
        }
    }

    //ユーザー情報を取得する
    static public function getUserInfo($user_id) {
        debug('ユーザー情報を取得します');
        try {
            $dbh = Db::dbConnect();
            $sql = 'SELECT username, password, account, age, pic, ck, cs, `at`, ats FROM users WHERE id =:u_id AND delete_flg=0';
            $data = array(':u_id' => $user_id);

            $stmt = Db::queryPost($dbh, $sql, $data);

            
            if($stmt) {
                debug('ユーザー情報の取得に成功しました');
                return $stmt->fetch(PDO::FETCH_ASSOC);

            }else{
                debug('クエリが失敗しました。');
                debug('失敗したクエリ：'.$sql);
                return false;
            }

        }catch(Exception $e){
            debug('Exceptionエラー');
            debug('行：'.__LINE__.'　関数：'.__FUNCTION__);
            debug('エラーメッセージ：'.$e->getMessage());
            return false;
        }
    }

    //ユーザーのtweet情報を取得する
    static public function getTweetInfo($user_id) {
        debug('ユーザーのツイート情報を取得します');
        try {
            $dbh = Db::dbConnect();
            $sql = 'SELECT count, ck, cs, at, ats FROM tweet WHERE id =:u_id';
            $data = array(':u_id' => $user_id);

            $stmt = Db::queryPost($dbh, $sql, $data);

            
            if($stmt) {
                if($stmt->rowCount() > 0){
                    debug('tweet情報を持っているユーザーです');
                    debug('ユーザーのツイート情報の取得に成功しました');
                    return $stmt->fetch(PDO::FETCH_ASSOC);
                }else {
                    debug('tweet情報を持っていません');
                    debug('tweetテーブルを追加します');

                    $sql_insert = 'INSERT INTO tweet (id) VALUES (:u_id)';
                    $data_insert = array(':u_id' => $user_id);
                
                    $stmt = Db::queryPost($dbh, $sql_insert, $data_insert);

                    if($stmt) {
                        debug('初めてのツイート：レコードを追加しました');
                        debug('追加レコード：'.$stmt->fetch(PDO::FETCH_ASSOC));
                        return array(
                            'count' => 1,
                            'id' => $user_id
                        );

                    }else{
                        debug('初めてのツイート：レコード追加できませんでした');
                        return false;
                    }
                }
                

            }else{
                debug('クエリが失敗しました。');
                debug('失敗したクエリ：'.$sql);
                return false;
            }

        }catch(Exception $e){
            debug('Exceptionエラー');
            debug('行：'.__LINE__.'　関数：'.__FUNCTION__);
            debug('エラーメッセージ：'.$e->getMessage());
            return false;
        }
    }

    //ユーザーのtweet情報を取得する
    static public function CountUpTweet($user_id) {
        debug('ユーザーのツイート回数を１増やします');
        try {
            $dbh = Db::dbConnect();
            $sql = 'SELECT count, ck, cs, at, ats FROM tweet WHERE id =:u_id';
            $data = array(':u_id' => $user_id);
            
            $stmt_select = Db::queryPost($dbh, $sql, $data);

            if($stmt_select) {
                if($stmt_select->rowCount() > 0){
                    debug('すでにtweetレコードが追加されています:rowCount()=>'.$stmt_select->rowCount());
                    //すでにレコードが登録されている（すでに1度以上、ツイートしている）
                    //ツイート回数を増やす
                    $rst = $stmt_select->fetch(PDO::FETCH_ASSOC);
                    $count = $rst['count'];
                    debug('今のツイート回数:'.$count);
                    $count_tweet_new = ++$count;
                    debug('プラスしたツイート回数:'.$count_tweet_new);
                    $sql = 'UPDATE tweet SET count=:count_new WHERE id =:u_id';
                    $data = array(':u_id' => $user_id, ':count_new' => $count_tweet_new);
                
                    $stmt = Db::queryPost($dbh, $sql, $data);
                    debug('ユーザーのツイート回数を１増やすのに成功しました');
                    return true;
                }else{
                    //レコードが存在しない
                    $sql = 'INSERT INTO tweet id VALUES :u_id ';
                    $data = array(':u_id' => $user_id);
                
                    $stmt = Db::queryPost($dbh, $sql, $data);

                    if($stmt) {
                        debug('初めてのツイート：レコードを追加しました');
                        debug('追加レコード：'.$stmt->fetch(PDO::FETCH_ASSOC));
                        return $stmt->fetch(PDO::FETCH_ASSOC);

                    }else{
                        debug('初めてのツイート：レコード追加できませんでした');
                        return false;
                    }

                }

            }else{
                debug('ユーザーのツイート回数を１増やすのに失敗しました');
                debug('失敗したクエリ：'.$sql);
                return false;
            }

        }catch(Exception $e){
            debug('Exceptionエラー');
            debug('行：'.__LINE__.'　関数：'.__FUNCTION__);
            debug('エラーメッセージ：'.$e->getMessage());
            return false;
        }
    }

}