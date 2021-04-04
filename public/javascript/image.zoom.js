$modal = $("#image-zoom");
if($modal.length) {
    $imageMediaLink = $("#medias a").parent();
    $imageMediaLink.click( function (e) {
        var $target = $(e.target);
        var src = $target.attr("src");
        if(!src) {
            src = $($target.find("[src]")).attr("src");
        }
        $modal.find("img").attr("src", src);
        $modal.modal('show');
        return false;
    });
}
