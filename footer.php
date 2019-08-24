<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script>
$(function(){
    //メッセージ表示
    setInterval(function() {
        $('.message-registered').addClass('fadeout');
    }, 3000);

    //ゴミ箱クリック動作
    $garbage = $('.garbage') || null;
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
    

})    
</script>