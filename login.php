<?php 

//共通変数・関数ファイルを読込み
require('functions.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ユーザー登録ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

if(!empty($_POST)){

    //フォームチェッカをnew
    $formchecker = new FormCheck();

    //フォームの入力内容取得
    $name = $_POST['name'];
    $pass = $_POST['pass'];

    //ニックネームフォームチェック
    $formchecker->CheckFormName($name, false);
    //passwordフォームチェック
    $formchecker->CheckFormpassword($pass);

    debug('フォームチェック完了！');
    debug('$formchecker=>>>'.print_r($global_msg, true));
    if(empty($global_msg)){        
        debug('フォームに問題ありませんでした');
        debug('----ログイン処理---');
        try {
            $dbh = Db::dbConnect();
            $sql = 'SELECT password,id FROM users WHERE username =:u_name AND delete_flg=0';
            $data = array(':u_name' => $name);

            $stmt = Db::queryPost($dbh, $sql, $data);

            $rst = $stmt->fetch(PDO::FETCH_ASSOC);

            
            if(!empty($rst)) {
                debug('クエリ成功');
                if(password_verify($pass, array_shift($rst))){
                    debug('ユーザー認証成功。');
                    $sestime = 60*60; //セッションの有効時間　デフォルト１時間
                    //login時間を記録
                    $_SESSION['login_time'] = time();//
    
                    $_SESSION['session_limit_time'] = $sestime; 
    
                    //チェックありなしでセッション時間を延ばす処理を入れてもいい
    
                    //セッションにユーザーIDを格納
                    $_SESSION['user_id'] = $rst['id'];
                    debug('$_SESSION["user_id"]=>>'.$rst['id']);
    
                    //登録完了メッセージ登録
                    setDispMessage('おかえりなさい！');
    
                    header("Location:main-board.php");
                }else{
                    debug('パスワードが一致しませんでした。');
                    $global_msg['common'] = Messages::MSG09;  
                }

            }else{
                debug('クエリが失敗しました。');
                debug('失敗したクエリ：'.$sql);
                $global_msg['common'] = Messages::MSG09;
            }

        }catch(Exception $e){
            debug('Exceptionエラー');
            debug('行：'.__LINE__.'　関数：'.__FUNCTION__);
            debug('エラーメッセージ：'.$e->getMessage());
        }

    }else{
        debug('フォーム入力エラー');

    }

}


?>

<?php 
require('head.php');
?>
    <main class="signup">
        <section class="main-board">          

            <h1>ログイン</h1>
            <form class="signup-board" action="" method="post">
                <div class="c-form form">
                    <label class="c-form__label" for="tweet-name">ニックネーム</label>
                    <input type="text" id="js-input-name" class="text-init" name="name" value="<?php echo empty($name) ? '' : $name; ?>">
                    <div class="c-area-msg">
                    <?php 
                    if(!empty($global_msg['name'])) echo $global_msg['name'];
                    ?>
                    </div>
                </div>
                
                <div class="c-form">
                    <label class="c-form__label" for="tweet-name">パスワード(半角6文字以上)</label>
                    <input type="password" class="text-init" name="pass" value="<?php echo empty($pass) ? '' : $pass; ?>">
                    <div class="c-area-msg">
                    <?php 
                    if(!empty($global_msg['pass'])) echo $global_msg['pass'];
                    ?>
                    </div>
                </div> 
                <div class="form">
                    <div class="c-area-msg">                
                        <?php 
                        if(!empty($global_msg['common'])) echo $global_msg['common'];
                        ?>
                    </div>
                    </div>
                </div>  
                <input type="submit" class="btn-start submit-init" value="GO" >
            </form>
        </section>
    </main>

    <footer>
        <ul class="footer-list">
            <li> <a class="hover" href="signup.php">新規登録</a></li>
        </ul>
    </footer>

    <?php require('footer.php'); ?>
  
</body>

</html>