<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script>
$(function(){
    //メッセージ表示
    setInterval(function() {
        $('.message-registered').addClass('fadeout');
    }, 3000);

    //ゴミ箱クリック動作
    $garbage = $('.js-garbage-delete') || null;
    console.log('ajax start!');
    $garbage.click(function(){
    deleteproductId= $(this).data('productid') || null;
    console.log('deleteproductid: ' + deleteproductId);
        $.ajax({
            type: "POST",
            url:  "deleteSubjectAjax.php",
            data: {productId : deleteproductId}
        }).done(function(data){
            // reloadメソッドによりページをリロード
            window.location.reload();
            console.log('Ajax Success');
        }).fail(function(msg){
            console.log('Ajax Error');
        });
    });

    //画像のドロップ
    $('.js-img-drop').on('dragover', function(e) {
        e.stopPropagation();
        e.preventDefault();
        $(this).css("border","2px dashed #aaa");
    });
    $('.js-img-drop').on('dragleave', function(e) {
        e.stopPropagation();
        e.preventDefault();
        $(this).css("border","2px dashed transparent");
    });

    $inputfile = $('.user-img__drop-input');
    $inputfile.on('change', function(e){
        e.stopPropagation();
        e.preventDefault();
        $('.js-img-drop').css("border","2px dashed transparent");

        var file = this.files[0], 
        $img = $(this).siblings('.user-img__img'), // 3. jQueryのsiblingsメソッドで兄弟のimgを取得
        fileReader = new FileReader();   // 4. ファイルを読み込むFileReaderオブジェクト

        console.log('file: ' + file);
        // 5. 読み込みが完了した際のイベントハンドラ。imgのsrcにデータをセット
        fileReader.onload = function(event) {
            // 読み込んだデータをimgに設定
            $img.attr('src', event.target.result).show();
        };

        // 6. 画像読み込み
        fileReader.readAsDataURL(file);


    });

    //
    //勉強時間測定
    //

    var $time_min = $('#js-timer-min');
    var $time_sec = $('#js-timer-sec');
    var $time_btn = $('#js-timer-btn');

    //クリック時の時間を保持
    var startTime;
    //経過時間
    var elapsedTime;    
    //タイマーを止めるにはclearTimeoutを使う必要があり、そのためにはclearTimeoutの引数に渡すためのタイマーのidが必要
    var timerId;
    //タイマーをストップ -> 再開させたら0になってしまうのを避けるための変数。
    var timeToadd = 0;
    //ミリ秒の表示ではなく、分とか秒に直すための関数, 他のところからも呼び出すので別関数として作る

    //データベース格納用の勉強時間変数
    var time_db;

    //計算方法として135200ミリ秒経過したとしてそれを分とか秒に直すと -> 02:15:200
    function updateTimetText(){

        //h(時)
        var h = Math.floor(elapsedTime / 3600000);

        //1時間（60分）を超えていた場合に以下の計算では60分未満となるようにする
        //今日の勉強時間で表示される時間はx分x秒であり、2時間の場合も分表示120分であるため
        elapsedTime= elapsedTime % 3600000;
        
        //m(分) = 135200 / 60000ミリ秒で割った数の商　-> 2分
        var m = Math.floor(elapsedTime / 60000);

        //s(秒) = 135200 / 1000ミリ秒
        var s = Math.floor(elapsedTime % 60000 / 1000);

        //HTML 上で表示の際の桁数を固定する　例）3 => 03　、 12 -> 012
        //javascriptでは文字列数列を連結すると文字列になる
        //文字列の末尾2桁を表示したいのでsliceで負の値(-2)引数で渡してやる。
        h = ('0' + h).slice(-2); 
        m = ('0' + m).slice(-2); 
        s = ('0' + s).slice(-2);

        //HTMLのid　timer部分に表示させる　
        $time_min.text(m);
        $time_sec.text(s);
    }

    //再帰的に使える用の関数
    function countUp(){

        //timerId変数はsetTimeoutの返り値になるので代入する
        timerId = setTimeout(function(){
            // console.log('elapseTime: ' + elapsedTime);
            //経過時刻は現在時刻をミリ秒で示すDate.now()からstartを押した時の時刻(startTime)を引く
            elapsedTime = Date.now() - startTime + timeToadd;
            updateTimetText()

            //countUp関数自身を呼ぶことで10ミリ秒毎に以下の計算を始める
            countUp();

        //1秒以下の時間を表示するために10ミリ秒後に始めるよう宣言
        },10);
    }

    //startボタンにクリック時のイベントを追加(タイマースタートイベント)
    $time_btn.on('click',function(){
        $this = $(this);

        if($this.hasClass('studying')){

            $this.removeClass('studying');
            $this.text('測定再開');
            //タイマーを止めるにはclearTimeoutを使う必要があり、そのためにはclearTimeoutの引数に渡すためのタイマーのidが必要
            clearTimeout(timerId);
            //タイマーに表示される時間elapsedTimeが現在時刻かたスタートボタンを押した時刻を引いたものなので、
            //タイマーを再開させたら0になってしまう。elapsedTime = Date.now - startTime
            //それを回避するためには過去のスタート時間からストップ時間までの経過時間を足してあげなければならない。elapsedTime = Date.now - startTime + timeToadd (timeToadd = ストップを押した時刻(Date.now)から直近のスタート時刻(startTime)を引く)
            timeToadd += Date.now() - startTime;

            //ajax通信　測定中止ボタン押下時に、勉強時間を更新する
            subId= $this.data('subid') || null;
            time_beore= $this.data('time') || null;

            //データベース格納用
            time_db = time_beore + elapsedTime; 

            console.log('time_db_ms:' + time_db);

            //h(時)
            var h_p = Math.floor(time_db / 3600000);            
            //m(分) = 135200 / 60000ミリ秒で割った数の商　-> 2分
            var m_p = Math.floor(time_db / 60000);
            //s(秒) = 135200 / 1000ミリ秒
            var s_p = Math.floor(time_db % 60000 / 1000);

            //HTML 上で表示の際の桁数を固定する　例）3 => 03　、 12 -> 012
            //javascriptでは文字列数列を連結すると文字列になる
            //文字列の末尾2桁を表示したいのでsliceで負の値(-2)引数で渡してやる。
            h_p = ('0' + h_p).slice(-2); 
            m_p = ('0' + m_p).slice(-2); 
            s_p = ('0' + s_p).slice(-2);

            //データベース格納用
            time_db = h_p + m_p + s_p;   

            console.log('time_db:' + time_db);

            console.log('subId'+subId);
            console.log('$time_db:'+time_db);
            $.ajax({
                type: "POST",
                url:  "setStudyTimeAjax.php",
                data: {
                    studyTime_total : time_db,//形式：012030⇒01時間20分30秒
                    studyTime_today : elapsedTime,//形式ms秒
                    subjectId : subId
                    }
            }).done(function(data){
                console.log('Ajax Success');
            }).fail(function(msg){
                console.log('Ajax Error');
            });

        }else{
            $this.addClass('studying');
            $this.text('測定中止');
            //現在時刻を代入
            startTime = Date.now();

            //時間の表示を更新する
            countUp();
        }
    });
})    
</script>