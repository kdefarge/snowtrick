var $htmlElement = $("#html_element");
if($htmlElement.length) {

    var public_key = $htmlElement.attr("data-public-key");
    var onloadCallback = function() {
        grecaptcha.render("html_element", {
            "sitekey" : public_key
        });
    };
    
    $.ajax({
        url: "https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit",
        dataType: "script",
        async: true,
        cache: true
    });
}
