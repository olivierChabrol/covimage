jQuery(function($) {

    $("#imgPos").html(parseInt($("#slider").val()));
    if ($("#visualizer-box").length) {
        token = $("#visualizer-box").attr('token');
        imgurl = $(".visualized-image").attr('src');
        imgurl = imgurl.substr(0,imgurl.lastIndexOf('/'))+'/';
    }
    $("#slider").on('input',function() {
        let pos = $(this).val();
            
        console.log("$(\"#slider\").on('input') pos : " + pos);
        let posInt = parseInt(pos);
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
        let infoPos = posInt -1;
        if (infoPos < 0) {
            infoPos = 0;
        }
        $(".visualized-image").css("z-index",100);
        $(".visualized-image[imagecount="+infoPos+"]").css("z-index",200);
        console.log(".visualized-image[imagecount="+infoPos+"] z-index : " + $(".visualized-image[imagecount="+infoPos+"]").css("z-index") + " / " + $(".visualized-image[imagecount="+infoPos+"]").attr("src"));
        while (pos.length<$("#total").html().length) {
            pos = "0"+pos;
        }
        $("#actual-pos").html(pos);
        $("#imgPos").html(posInt);
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
    $("#hideShowLayerButton").click(function () {
        toggleLayer();
    });

    $("#start-processing").click(function(){
        
        $.post("/ajax-start-processing",{token: $(this).attr('token'), command: $("#command").val(), },function (response) {
            if (response.success) {
                $("#processing-state").html("Processing finished");
                $("#loading_icon").hide();
                let bak = $("#processing-message").html().split("<a ")[1];
                $("#processing-message").html('Results are available <a '+bak);
            } else {
                $("#processing-state").html("Processing failed");
            }
        });
        $("#processing-state").html('Processing started');
        $("#loading_icon").show();
        $("#start-processing").hide();
    });

    function toggleLayer() {
        let posInt = parseInt($("#slider").val());
        let infoPos = posInt -1;
        if (infoPos < 0) {
            infoPos = 0;
        }
        let imageId = "#layer"+infoPos;
        console.log("[toggleLayer("+infoPos+")] " + imageId + ": '" + $(imageId).is(":visible")+"'");
            if($(imageId).is(":visible")){
                $(imageId).hide();
            }
            else
            {
                $(imageId).show();
            }
    }

    $(document).on('keypress', function(e) {
        var tag = e.target.tagName.toLowerCase();
        if ( e.which === 119 && tag != 'input' && tag != 'textarea') 
        {
            toggleLayer();
        }
    });
});