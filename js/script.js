$(function(){
    /*-------------------------------------------
    index.phpで使用する処理
    ---------------------------------------------*/ 
    $('.category').hover(
        function(){
            $(this).css('overflow','hidden');
            $(this).children('img').css('transition-duration','0.3s');
            $(this).children('img').css('transform','scale(1.2)');
        },
        function(){
            $(this).children('img').css('transform','scale(1.0)');
        }
    );
    /*-------------------------------------------
    thread.phpで使用する処理
    ---------------------------------------------*/
    //goodCommentId = コメントID
    var $good = $('.good-btn'),goodCommentId;
    $good.on('click',function(e){
        //親要素への伝播をキャンセルする
        e.stopPropagation();
        var $this = $(this);
        goodCommentId = $(this).parents('.cmmt').data('commentid');
        $.ajax({
            type: 'POST',
            url: 'good.php',
            data: { commentId : goodCommentId }
        }).done(function(data){
            //いいねの総数を取得
            $this.children('span').html(data);
            //いいねを取り消す
            $this.children('i').toggleClass('far');
            //いいねを押す
            $this.children('i').toggleClass('fas');
            $this.children('i').toggleClass('active');
            $this.toggleClass('active');
        }).fail(function(msg){
            console.log('エラーが発生しました');
        });
    });
});