<?php 

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　認証ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

if(!empty($_SESSION['login_time'])){
    debug('ログインユーザーです');

    //ログインの時間が制限以上経過していないかチェック
    if($_SESSION['login_time'] + $_SESSION['session_limit_time'] > time()){
        //制限時間内だった　login_timeを更新する
        debug('制限時間内です。');
        $_SESSION['login_time'] = time();

        if(basename($_SERVER['PHP_SELF']) === 'login.php'){
            header("Location:main-board.php");
        }
    }else{
        debug('制限時間を超過しています');
        //制限時間を超過していた
        session_destroy();//セッションを削除　ログアウト処理

        //loginページに遷移させる
        debug('ログインページへ遷移します');
        $msg_global->$display_msg = 'ログインしてください';
        header("Location:login.php?message=1");
    }
}else {
    debug('未ログインユーザーです');
    if(basename($_SERVER['PHP_SELF']) !== 'login.php') {
        $msg_global->$display_msg = 'ログインしてください';
        header('Location:login.php?message=1');
    }
}
