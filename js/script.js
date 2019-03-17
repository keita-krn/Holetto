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
        //親要素への伝播をキャンセルする。
        e.stopPropagation();
        var $this = $(this);
        goodCommentId = $(this).parents('.cmmt').data('commentid');
        $.ajax({
            type: 'POST',
            url: 'good.php',
            data: { commentId : goodCommentId }
        }).done(function(data){
            console.log('Ajax Success');
            //いいねの総数を取得
            $this.children('span').html(data);
            //いいねを取り消す
            $this.children('i').toggleClass('far');
            //いいねを押す
            $this.children('i').toggleClass('fas');
            $this.children('i').toggleClass('active');
            $this.toggleClass('active');
        }).fail(function(msg){
            console.log('Ajax Error');
        });
    });

    //コメントを削除する場合
    var $delete = $('#delete').attr('href');
    $('#delete').click(function(){
        var res = confirm("本当に削除してよろしいですか？");
        if(res){
            location.href = $delete;
        }else{
            alert("削除を取り消しました。");
        }
    });
});