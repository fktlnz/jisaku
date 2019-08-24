<?php 

//共通変数・関数ファイルを読込み
require('functions.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　勉強項目ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//認証処理
require('auth.php');

$sub_id = empty($_GET['sub_id']) ? '' : $_GET['sub_id'];
$u_id = empty($_SESSION['user_id']) ? '' : $_SESSION['user_id'];

$subject_ttl = Db::getSubject($sub_id, $u_id);

if(!empty($_POST)){



}




?>

<?php require('head.php') ?>

    <main class="subject">
        <section class="main-board">
            <h1><?php echo $subject_ttl['name']; ?></h1>
            <div class="subject-list">
                <div class="subject-list__time-all">
                    <h1>総勉強時間</h1>
                    <ul class="subject-list__time-all-item">
                        <li><h3>Total:</h3></li>
                        <li id="subject-list__time-all-item-hour"><h3>20</h3></li>
                        <li><h3>h</h3></li>
                        <li id="subject-list__time-all-item-min"><h3>40</h3></li>
                        <li><h3>min</h3></li>
                    </ul>
                </div>
                <div class="subject-list__time-today">
                    <h1>今日の勉強時間</h1>
                    <ul class="subject-list__time-today-item">
                        <li><h3>Total:</h3></li>
                        <li id="subject-list__time-today-item-hour"><h3>2</h3></li>
                        <li><h3>h</h3></li>
                        <li id="subject-list__time-today-item-min"><h3>40</h3></li>
                        <li><h3>min</h3></li>
                    </ul>
                    <div id="start-timer"><span class="hover">測定開始</span></div>
                </div>
            </div>
            <div class="to-tweet-scr">
                <a class="hover" href="tweet.php"><span>投稿画面へ</span></a>
            </div>
        </section>
    </main>
    
</body>
</html>