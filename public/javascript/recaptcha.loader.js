var $htmlElement = $("#html_element");
if($htmlElement.length) {

    var publicKey = $htmlElement.attr("data-public-key");
    var onloadCallback = function() {
        grecaptcha.render("html_element", {
            "sitekey" : publicKey
        });
    };
    
    $.ajax({
        url: "https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit",
        dataType: "script",
        async: true,
        cache: true
    });
}
