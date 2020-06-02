jQuery(function($) {
    if ($("#visualizer-box").length) {
        token = $("#visualizer-box").attr('token');
        imgurl = $(".visualized-image").attr('src');
        imgurl = imgurl.substr(0,imgurl.lastIndexOf('/'))+'/';
    }
    $("#slider").on('input',function() {
        pos = $(this).val();
        posInt = parseInt(pos);
        for ( let i = posInt-10>0 ? posInt-10 : 1 ; i < (posInt+10>parseInt($("#total").html()) ? parseInt($("#total").html()) : posInt+10); i++) {
            if ($(".visualized-image[imagecount="+i+"]").length==0) {
                $("#images_stack").append('<img class="visualized-image" imagecount="'+i+'" src="'+imgurl+'image_'+i+'.png" style="z-index: 100;">');
            }
        }
        $(".visualized-image").each(function() {
            if(parseInt($(this).attr('imagecount'))>(posInt+10) || parseInt($(this).attr('imagecount'))<(posInt-10)) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
        $(".visualized-image").css("z-index",100);
        $(".visualized-image[imagecount="+pos+"]").css("z-index",200);
        while (pos.length<$("#total").html().length) {
            pos = "0"+pos;
        }
        $("#actual-pos").html(pos);
    });
    $("#check_analyser").click(function (){
        $.post("/ajax-check-state",{token: $(this).attr('token')},function (response) {
            if (response.success) {
                $("#analyse_state").html("Processing finished : <a href='"+document.URL+"'>Results</a>");
            } else {
                $("#analyse_state").html("Processing in progress...");
            }
        })
    });
    $("#start-processing").click(function(){
        $.post("/ajax-start-processing",{token: $(this).attr('token')},function (response) {
            if (response.success) {
                $("#processing-state").html("Processing finished");
                let bak = $("#processing-message").html().split("<a ")[1];
                $("#processing-message").html('Results are available <a '+bak);
            } else {
                $("#processing-state").html("Processing failed");
            }
        });
        $("#processing-state").html('Processing started');
    });
});