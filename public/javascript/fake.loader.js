var discussionMax = 5;
var $messageCollection = $("#discussions .discussion-message");
if($messageCollection.length > discussionMax) {
    var currentLoaded = discussionMax;
    for (var i = discussionMax; i < $messageCollection.length; i++) {
        $($messageCollection.get(i)).hide();
    }
    var $extendButton = $("<button class=\"btn btn-primary btn-lg active\" type=\"button\">Charger plus</button>");
    $extendButton.on("click", function(e) {
        for (var i = currentLoaded; i < currentLoaded+discussionMax; i++) {
            var current = $messageCollection.get(i);
            if(!current) {
                break;
            }
            $(current).show();
        }
        currentLoaded+=discussionMax;
        if(currentLoaded>=$messageCollection.length) {
            this.remove();
        }
    });
    $("#discussions").append($extendButton);
}
