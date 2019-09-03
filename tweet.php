<?php 

//共通変数・関数ファイルを読込み
require('functions.php');



debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ツイート画面　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//認証処理
require('auth.php');

// twitter api を使うための準備
require 'vendor/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

//ユーザーIDを取得
$u_id = empty($_SESSION['user_id']) ? '' : $_SESSION['user_id'];

//ユーザー情報をデータベースから取得する
$user_info = Db::getUserInfo($u_id);

//twitter developer keyを取得
$consumerKey = $user_info['ck'];
$consumerSecret = $user_info['cs'];
$accessToken = $user_info['at'];
$accessTokenSecret = $user_info['ats'];
debug('consumerKey:'.$consumerKey);
debug('consumerSecret:'.$consumerSecret);
debug('accessToken:'.$accessToken);
debug('accessTokenSecret:'.$accessTokenSecret);

// consumerKey:8v4H4Awp2XiXAUDw7dGbg6G78
// consumerSecret:e3wYl1I580Ryl5v5STM32L3nD2wJTTt7PIeuYZYvLBXyHi2VUw
// accessToken:296853080-Av5tqYOqD3Dd1iFPfFrcj6SNUSlcDMkjcO3JrZz2
// accessTokenSecret:I0L4XYrBw6m6v8RZyPGilVagVWayrjIHGhtYfHFnMuwfI

$hasKey;//twitterAPIKeyを登録しているか

if(!empty($consumerKey) && !empty($consumerSecret) && !empty($accessToken) && !empty($accessTokenSecret)) {
    //全てemptyでない場合
    // twitterapi認証処理
    $connection = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);      
    $hasKey=true;
}else {
    $hasKey=false;
}

if(!empty($_POST)){
    debug('tweetが送信されました');
    debug('TwitterOAuthでpost処理します');
    //tweetする
    $result = $connection->post("statuses/update", array("status" => $_POST['tweet-text']));
    debug('$HTTP-Code:'.$connection->getLastHttpCode(), false);
    if($connection->getLastHttpCode() == 200) {
        debug('ツイートに成功しました');
        $rst = Db::CountUpTweet($u_id);
        if($rst) {
            debug('ツイートの回数をカウントアップしました');
            setDispMessage('ツイート完了しました！');
            header("Location:main-board.php");
        }else {
            debug('ツイートの回数のカウントアップに失敗しました');
        }
    }else{
        debug('ツイートに失敗しました');
        setDispMessage('ツイートに失敗しました。TwitterAPIの登録内容をご確認ください。');
        header("Location:main-board.php");
    }
    
}else {
    //勉強日数（ツイート回数）を取得
    $tweet_info = Db::getTweetInfo($u_id);
    $day = $tweet_info['count'];
    //勉強項目のidを取得
    $sub_id = empty($_GET['sub_id']) ? '' : $_GET['sub_id'];
    $subject = Db::getSubject($sub_id, $u_id);
    //項目の名前を取得
    $subject_ttl = $subject['name'];    
    //勉強時間を取得　HH:MM:SS
    //[HH, MM, SS]
    $subject_time = explode(":", $subject['time']);
    //今日の勉強時間をセッションから取得する ms秒からHMS形式に変換
    $studyTime_today = getHMSTime($_SESSION['studyTime_today']);
}







?>


<?php 
require('head.php');
?>

    <main class="tweet">
        <section class="main-board">
            <h1>勉強したことをツイートしよう！</h1>
            <form class="tweet-board" action="" method="POST">                
                <div class="tweet-board__text">
                    <textarea class="textarea-init js-tweet-board__textarea mb10" name="tweet-text" id="" cols="30" rows="10">
#ウェブカツ
DAY <?php echo $day ?>

<?php echo $subject_ttl; ?>

Today <?php echo (empty($studyTime_today[0])) ? '0' : $studyTime_today[0]; ?>時間 <?php echo empty($studyTime_today[1]) ? '0' : $studyTime_today[1]; ?>分 <?php echo empty($studyTime_today[2]) ? '0' : $studyTime_today[2]; ?>秒
Total <?php echo (empty($subject_time[0])) ? '0' : $subject_time[0]; ?>時間 <?php echo empty($subject_time[1]) ? '0' : $subject_time[1]; ?>分</textarea>
                    <p class="tweet-board__limittext" style="font-size: 80%;"><span class="js-tweet-board__textcount">78</span>/280文字<br>（半角：1文字、全角：2文字カウント）</p>
                </div>
<?php if($hasKey): ?>
                <input type="submit" class="btn-tweet submit-init js-tweet-submit-disabled" value="投稿する" >
<?php else: ?>
                <a class="js-tweet-add-link btn-tweet-display" href="">投稿画面を表示</a>
<?php endif; ?>
            </form>
        </section>
    </main>
    <?php require('footer.php'); ?>
</body>
</html>