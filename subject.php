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

$subject = Db::getSubject($sub_id, $u_id);

//項目の名前を取得
$subject_ttl = $subject['name'];
//勉強時間を取得　HH:MM:SS
//[HH, MM, SS]
$subject_time = explode(":", $subject['time']);
$subject_time_ms = $subject_time[0] * 3600000 + $subject_time[1] * 60000 +$subject_time[2] * 1000;

if(!empty($_POST)){
}




?>

<?php require('head.php') ?>

    <main class="subject">
        <section class="main-board">
            <h1><?php echo $subject_ttl; ?></h1>
            <div class="subject-list">
                <div class="subject-list__time-all">
                    <h1>総勉強時間</h1>
                    <ul class="subject-list__time-all-item">
                        <li><h3>Total:</h3></li>
                        <li id="subject-list__time-all-item-hour"><h3><?php echo $subject_time[0] ?></h3></li>
                        <li><h3>時間</h3></li>
                        <li id="subject-list__time-all-item-min"><h3><?php echo $subject_time[1] ?></h3></li>
                        <li><h3>分</h3></li>
                    </ul>
                </div>
                <div class="subject-list__time-today">
                    <h1>今日の勉強時間</h1>
                    <ul class="subject-list__time-today-item mb30">
                        <li><h3>Total:</h3></li>
                        <li id="subject-list__time-today-item-hour"><h3 id="js-timer-hour">00</h3></li>
                        <li><h3>時間</h3></li>
                        <li id="subject-list__time-today-item-min"><h3 id="js-timer-min">00</h3></li>
                        <li><h3>分</h3></li>
                        <li id="subject-list__time-today-item-sec"><h3 id="js-timer-sec">00</h3></li>
                        <li><h3>秒</h3></li>
                    </ul>
                    <div id="start-timer"><span id="js-timer-btn" class="hover" data-subid="<?php echo $sub_id; ?>" data-time="<?php echo $subject_time_ms; ?>"><i class="far fa-clock" style="margin-right: 5px;"></i>測定開始</span></div>
                </div>
            </div>
            <div class="to-tweet-scr">
                <a class="hover" href="tweet.php?<?php echo 'sub_id='.$sub_id;?>"><span><i class="fab fa-twitter" style="margin-right:5px; color:#2ebafc;"></i>投稿する</span></a>
            </div>
        </section>
    </main>
    
</body>
<?php require('footer.php') ?>
</html>