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
    $pass_re = $_POST['pass_re'];
    $account = $_POST['account'];
    
    //ニックネームフォームチェック
    $formchecker->CheckFormName($name, true);
    //passwordフォームチェック
    $formchecker->CheckFormpassword($pass);
    //accountフォームチェック(passwordチェックと同じものを使用) 
    //$formchecker->CheckFormpassword($account);
    //password再入力フォームチェック
    $formchecker->validMatch($pass, $pass_re, 'pass_re');

    debug('$err_msg:'.print_r($global_msg, true));
    if(empty($global_msg)){        
        debug('----ユーザー登録処理---');
        try {
            $dbh = Db::dbConnect();
            $sql = 'INSERT INTO users (username, password, account, create_date) VALUES (:name, :pass, :account, :create_date)';
            $data = array(':name' => $name, ':pass' => password_hash($pass, PASSWORD_DEFAULT), ':account' => $account, ':create_date' => date('Y-m-d H:i:s'));

            $stmt = Db::queryPost($dbh, $sql, $data);

            if($stmt) {
                debug('ユーザーの登録が完了しました。');
                $_SESSION['login_time'] = time();

                //制限時間を延ばす処理をいれてもいい

                $_SESSION['session_limit_time'] = 60*60;

                $_SESSION['user_id'] = $dbh->lastInsertId();

                //登録完了メッセージ格納
                setDispMessage('登録完了しました！ご自由にどうぞ！');
                header('Location:main-board.php');

            }else{
                debug('クエリが失敗しました。');
                debug('失敗したクエリ：'.$sql);
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

<?php require('head.php');?>

    <main class="signup">
        <section class="main-board">
            <h1>ユーザー登録</h1>
            <form class="l-main-bord signup-board" action="" method="post">
                <div class="c-form">
                    <label class="c-form__label" for="tweet-name">ニックネーム(必須)</label>
                    <span id="use-okng"></span>
                    <input type="text" id="js-input-name" class="text-init" name="name" value="<?php echo empty($name) ? '' : $name; ?>">
                    <div class="c-area-msg">
                    <?php 
                    if(!empty($global_msg['name'])) echo $global_msg['name'];
                    ?>
                    </div>
                </div>
                
                <div class="c-form ">
                    <label class="c-form__label" for="tweet-name">パスワード(半角6文字以上)(必須)</label>
                    <input type="password" class="text-init" name="pass" value="<?php echo empty($pass) ? '' : $pass; ?>">
                    <div class="c-area-msg">
                    <?php 
                    if(!empty($global_msg['pass'])) echo $global_msg['pass'];
                    ?>
                    </div>
                </div>
                
                <div class="c-form">
                    <label for="tweet-name">パスワード再入力(必須)</label>
                    <input type="password" class="text-init" name="pass_re" value="<?php echo empty($pass_re) ? '' : $pass_re; ?>">
                    <div class="c-area-msg">
                    <?php 
                    if(!empty($global_msg['pass_re'])) echo $global_msg['pass_re'];
                    ?>
                    </div>
                </div>
                
                <div class="c-form">
                    <label for="tweet-name">ツイッターアカウント(必須)</label>
                    <input type="text" class="text-init" name="account" value="<?php echo empty($account) ? '' : $account; ?>">
                    <div class="c-area-msg">
                    <?php 
                    if(!empty($global_msg['account'])) echo $global_msg['account'];
                    ?>
                    </div>
                </div>                
                
                <input type="submit" class="btn-start submit-init" value="始める" >
            </form>
        </section>
    </main>

    <footer>
        <ul class="footer-list">
            <li> <a class="hover" href="login.php">ログイン</a></li>
        </ul>
    </footer>

    <?php require('footer.php') ?>

    <script src="js/vendor/jquery-2.2.2.min.js"></script>
    <script>
    $(function(){

        //フォームのユーザー名の重複チェック
        var user_name = '';
        $('#js-input-name').on('keyup', function(){
            user_name = $(this).val();
            console.log('$user_name:' + user_name);
            $.ajax({
                type: "POST",
                url: "ajaxUsernameChk.php",
                data: { username_ajax : user_name},
                dataType: "text"
            }).done(function(data){
                console.log('ajax通信が成功しました。');
                console.log('data: '+data);
                //文字変更処理
                if(data==='true'){
                    console.log('ニックネームが重複しています。');
                    $('#use-okng').html('使用不可です..').css('color','red');
                }else{
                    $('#use-okng').html('使用可能です!').css('color','green');
                    console.log('新規のニックネームです。');
                }
                
                
                
            }).fail(function(msg){
                console.log('ajax通信に失敗しました。');

            });



        });

    })
</script>
</body>

</html>