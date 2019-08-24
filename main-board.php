<?php 

//共通変数・関数ファイルを読込み
require('functions.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　メインボードページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//認証処理
require('auth.php');

$user_id = !empty($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

//メッセージ内容取得（セッション）
//$display_msg = !empty($_SESSION['msg']) ? getDispMessage() : '';

//勉強の項目を取得する
$all_subject = Db::getAllSubject($user_id);

debug('取得した全項目: '.print_r($all_subject, true));


?>


<?php 
require('head.php');
?>

    <main class="board">

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
            <h1>総勉強時間</h1>
            <ul class="all-time">
                <li><h3>Total</h3></li>
                <li id="all-time-hour"><h3>20</h3></li>
                <li><h3>h</h3></li>
                <li id="all-time-hour"><h3>30</h3></li>
                <li><h3>min</h3></li>
            </ul>
            <div class="subject-list">
                <ul class="subject-list__contents">
                    <!--PHPでループする-->
                    <?php
                    if(empty($all_subject)){
                        echo '<li style="color:#aaa;">勉強項目が追加されていません。「＋追加」から項目を登録してください。</li>' ;
                    }
                    
                    foreach($all_subject as $key => $data):
                    ?>
                    <a href="<?php echo 'subject.php?sub_id='.$data['id']?>">
                        <li class="subject-list__item">
                            <ul>
                                <li class="subject-list__circle"></li>
                                <li class="subject-list__subject"><?php echo $data['name'] ?></li>
                                <li class="subject-list__time" ><?php echo $data['time'] ?></li>
                                <li class="subject-list__garbage hover" data-productid="<?php echo !empty($data['id']) ? $data['id'] : '' ?>" ><i class="fas fa-trash" style="color:#aaa"></i></li>
                            </ul>
                        </li>
                    </a>
                    <?php
                    endforeach;
                    ?>
                    <!--ループ終了-->
                </ul>
                <div class="subject-list__add-subject"><a class="hover" href="add-subject.php">＋追加</a></div>
            </div>
        </section>
    </main>

    <footer>
        <ul class="footer-list">
            <li> <a class="hover" href="user-form.php">登録変更</a></li>
            <li> <a class="hover" href="logout.php">ログアウト</a> </li>
        </ul>
    </footer>

    <?php require('footer.php'); ?>
    
</body>
</html>