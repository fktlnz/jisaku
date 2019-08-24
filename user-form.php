<?php 
//共通変数・関数ファイルを読込み
require('functions.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ユーザー登録ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//フォームチェッカをnew
$formchecker = new FormCheck();

//ユーザー情報を取得する
$user_id = !empty($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
$dbFormData = Db::getUserInfo($user_id);
//ユーザー名
$name = $dbFormData['username'];


if(!empty($_POST)){
    debug('post送信がありました');
    debug('FILES情報: '.print_r($_FILES, true));
    //POSTから情報を取得する
    //ユーザー名 変更できないようにする
    //$name = $_POST['name'];
    //password
    //$pass = $_POST['pass'];
    //pasword再入力
    //$pass_re = $_POST['pass_re'];

    //twitterアカウント
    $account = !empty($_POST['account']) ? $_POST['account'] : '';
    if($account !== $dbFormData['account']){
        //accountフォームチェック(passwordチェックと同じものを使用) 
        $formchecker->validHalf($account, 'account');//半角チェック
        $formchecker->validMaxLen($account, 'account');//最大文字数チェック
    }
    //age
    $age = !empty($_POST['age']) ? $_POST['age'] : '';

    //password再入力フォームチェック
    //$formchecker->validMatch($pass, $pass_re, 'pass_re');

    $pic = !empty($_FILES['pic']['name']) ? upLoadImg($_FILES['pic']) : getUserForm('pic');
    debug('$pic: '.print_r($pic, true));
    debug('$_FILES: '.print_r($_FILES['pic'], true));

    if(empty($global_msg)){
        debug('フォームチェックOK');
        debug('ユーザー情報の変更を実施します。');

        //ユーザーidをセッションから取得
        $user_id = $_SESSION['user_id'];

        try {
            $dbh = Db::dbConnect();
            $sql = 'UPDATE users SET account=:account, age=:age, pic=:pic WHERE id =:u_id AND delete_flg=0';
            $data = array(':u_id' => $user_id, ':account' => $account, ':age' => $age, ':pic' => $pic);
            
            $stmt = Db::queryPost($dbh, $sql, $data);

            if($stmt) {
                debug('ユーザー登録を変更しました');
                setDispMessage('変更完了しました！');

            }else {
                debug('クエリに失敗しました');
                debug('失敗したクエリ：'.$sql);
                setDispMessage('変更にしました。。');
            }


        }catch(Exception $e) {
            debug('エラー発生！：'.$e->getMessage());
        }

    }

}else{
    //POSTされていないときは、DBからユーザー情報を取得して表示する

    

    
    //password
    $pass = getUserForm('password');
    //twitterアカウント
    $account = getUserForm('account');
    //age
    $age = getUserForm('age');
    //画像パス
    $pic = getUserForm('pic');
}

?>


<?php require('head.php') ?>

    <main class="user-form">
        <section class="main-board">
            <?php     
                $message = getDispMessage();
                if(!empty($message)){
                    debug('メッセージを表示します。');
                    debug('メッセージ内容：'.print_r($message, true));
                    $mark = '<h1 class="message-registered">'.$message.'</h1>';
                    echo $mark;
                }            
            ?>
            <h1>ユーザー情報変更</h1>
            <div class="user-form-board">

                <form class="l-main-board user-form-board" action="" method="POST" enctype="multipart/form-data">
                    <label action="" class="user-img  js-img-drop">
                        <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                        <input name="pic" type="file" class="user-img__drop-input">
                        <img class="user-img__img" src="<?php echo !empty($pic) ? $pic : getUserForm('pic'); ?>" alt="ユーザー画像" width="100" height="100">
                        <div class="c-area-msg">
                        <?php 
                        if(!empty($global_msg['img'])) echo $global_msg['img'];
                        ?>
                        </div>
                    </label>
                    <div class="c-form txt_left">
                        <label class="c-form__label" for="tweet-name">ニックネーム 
                        </label>
                        <input style="background-color:#eee;" type="text" id="js-input-name" class="text-init" name="name" value="<?php echo getUserForm('username'); ?>">
                        <div class="c-area-msg">
                        <?php 
                        if(!empty($global_msg['name'])) echo $global_msg['name'];
                        ?>
                        </div>
                    </div>   

                    <!-- <div class="user-form-board__password form">
                        <label for="tweet-name">パスワード(半角6文字以上)</label>
                        <input type="password" class="text-init" name="pass" value="<?php echo empty($pass) ? '' : $pass; ?>">
                        <div class="c-area-msg">
                        <?php 
                        if(!empty($global_msg['pass'])) echo $global_msg['pass'];
                        ?>
                        </div>
                    </div>
                    
                    <div class="user-form-board__password-re form">
                        <label for="tweet-name">パスワード再入力</label>
                        <input type="password" class="text-init" name="pass_re" value="<?php echo empty($pass_re) ? '' : $pass_re; ?>">
                        <div class="c-area-msg">
                        <?php 
                        if(!empty($global_msg['pass_re'])) echo $global_msg['pass_re'];
                        ?>
                        </div>
                    </div> -->

                    <div class="c-form txt_left">
                        <label for="tweet-name">ツイッターアカウント</label>
                        <input type="text" class="text-init" name="account" value="<?php echo getUserForm('account'); ?>">
                        <div class="c-area-msg">
                        <?php 
                        if(!empty($global_msg['account'])) echo $global_msg['account'];
                        ?>
                        </div>
                    </div> 

                    <div class="c-form txt_left">
                        <label for="tweet-name">年齢</label>
                        <input type="text" class="text-init" name="age"  value="<?php echo getUserForm('age'); ?>">                        
                    </div>
    
                    <input type="submit" class="btn-change submit-init" value="変更する" >
                </form>
            </div>
        </section>
    </main>

    <footer>
        <ul class="footer-list">
            <li> <a class="hover" href="main-board.php">管理リストに戻る</a></li>
        </ul>
    </footer>

    <?php require('footer.php') ?>
    
</body>
</html>