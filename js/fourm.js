var currentPage = 1;
$(function () {  
    function getData(pagenumber) {  
        currentPage++;
        $.get("ajax.php?mod=post&action=view", { page: pagenumber }, function (data) {  
            if (data.length > 0) {  
                var jsonObj = JSON.parse(data);  
                insertDiv(jsonObj);  
            }  
        });  
        $.ajax({  
            type: "post",  
            url: "ajax.php?mod=post&action=view",  
            data: { page: pagenumber },  
            dataType: "json",  
            success: function (data) {  
                $("#loadDiv").hide();  
                if (data.length > 0) {  
                    var jsonObj = JSON.parse(data);  
                    insertDiv(jsonObj);  
                }  
            },  
            beforeSend: function () {  
                $("#loadDiv").show();  
            },  
            error: function () {  
                $("#loadDiv").hide();  
            }  
        });  
    }
    getData(1);  
    function insertDiv(json) {  
        var listDiv = $("#listDiv");  
        var html = '';
		var posts = json.posts;
        for (var i = 0; i < posts.length; i++) {  
			html += '<div class="postcard" id="post'+ posts[i].tid +'"><div class="postcard-title"><div class="pull-left">';
			html += '<img class="img-responsive img-circle img-avatar" src="api.php?mod=user&action=getavatar&username='+ posts[i].username +'">';
			html += '</div><div class="postcard-info">';
			html += '<div>'+ posts[i].nickname +'(' + posts[i].username + ')</div>';
			html += '<div class="postcard-time">'+ (new Date(posts[i].timeline*1000)).toLocaleString() +'</div>';
			html += '</div></div>';
			html += '<div class="postcard-body" onclick="window.location.href=\'index.php?mod=post&action=viewpost&tid='+ posts[i].tid +'\'">';
			html += '<h3><a href="index.php?mod=post&action=viewpost&tid='+ posts[i].tid +'">'+ posts[i].title +'</a></h3>';
			html += '<p>'+posts[i].message+'</p>';
			html += '</div></div>';
        }
        listDiv.append(html);
    }
    var winH = $(window).height();
	
    var scrollHandler = function () {
        var pageH = $(document.body).height();
        var scrollT = $(window).scrollTop();
        var aa = (pageH - winH - scrollT) / winH;
        if (aa < 0.02) {
            if (currentPage % 10 === 0) {
                getData(currentPage);
                $(window).unbind('scroll');
                //$("#btn_Page").show();
            } else {
                getData(currentPage);
                //$("#btn_Page").hide();
            }  
        }  
    }  
    $(window).scroll(scrollHandler);  
  
    //$("#btn_Page").click(function () {  
    //    getData(i);  
    //    $(window).scroll(scrollHandler);  
    //});  
});  