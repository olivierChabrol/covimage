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
        console.log($("#password1").val());
        console.log($("#password2").val());
        if ($("#password1").val() == $("#password2").val()) {
            console.log("je modifie la bdd");
            $.post("/update",{'user': $(this).attr('user_id'),
                              'password': $("#password1").val()}, function (response) {
                if (response.success) {
                    alert("bdd modifiée");
                } else {
                    console.log("mot de passes différents");
                }
            });
        }
    });
});
