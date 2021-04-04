$(".fake-loader").each(function( index, collection ) {
    var $collection = $(collection);
    var itemMax = $collection.data("fakeloader-number");
    itemMax = (itemMax)?parseInt(itemMax):5;
    var $elements = $collection.children();
    if($elements.length > itemMax) {
        var currentLoaded = itemMax;
        for (var i = itemMax; i < $elements.length; i++) {
            $($elements.get(i)).hide();
        }
        var $extendButton = $("<div class=\"text-center col-12\"><button class=\"btn btn-primary btn-lg active\" type=\"button\">Charger plus</button></div>");
        $extendButton.on("click", function(e) {
            if($elements.length > currentLoaded+itemMax) {
                for (var i = currentLoaded; i < currentLoaded+itemMax; i++) {
                    var current = $elements.get(i);
                    $(current).show();
                }
                currentLoaded+=itemMax;
            } else {
                $elements.show();
                this.remove();
            }
        });
        $collection.after($extendButton);
    }
});
