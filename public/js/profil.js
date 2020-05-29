jQuery(function ($){
    
    $("#password2").blur(function() {
        if ($(this).val() == "") {
            $("#submitBtn").removeClass("btn-primary");
            $("#submitBtn").addClass("btn-secondary");
            $('#submitBtn').prop("disabled", true); //now disable
        } else {
            $("#submitBtn").removeClass("btn-secondary");
            $("#submitBtn").addClass("btn-primary");
            $('#submitBtn').prop("disabled", false); // now enable
        }
    });

    $("#submitBtn").click(function() {
        if ($("#password1") == $("#password2")) {
            // traitement bdd
        } else {
            //form control : mot de passes différents
        }
    });


});