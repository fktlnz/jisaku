<?php 

//共通変数・関数ファイルを読込み
require('functions.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　Ajax　setStudyTime');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

if(isset($_POST['subjectId']) && isset($_POST['studyTime_total']) && isset($_POST['studyTime_today']) && isset($_SESSION['user_id'])){
    debug('Ajax処理を開始');

    $s_time = $_POST['studyTime_total'];
    //セッションに今日の勉強時間を格納する
    $_SESSION['studyTime_today'] = $_POST['studyTime_today'];
    $sub_id = $_POST['subjectId'];
    debug('studyTime_total:'.$s_time);
    debug('subjectid:'.$sub_id);
    debug('これだけ勉強しましたー＞'.$s_time);
    $rst = Db::setStudyTime($sub_id, $_SESSION['user_id'], $s_time);
    if($rst) {
        debug('データベース更新成功：勉強時間');        
    }else{
        debug('データベース更新失敗：勉強時間');
    }
}