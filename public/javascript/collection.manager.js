function addTagFormDeleteLink($element) {
    var $removeFormButton = $("<button class=\"btn btn-warning\" type=\"button\">Supprimer le champ ci-dessus</button>");
    $element.append($removeFormButton);
    $removeFormButton.on("click", function(e) {
        $element.remove();
    });
}

function addFormToCollection($collection) {
    var prototype = $collection.data("prototype");
    var index = $collection.data("index");
    var $newForm = null;
    do {
        var newForm = prototype;
        $newForm = $(newForm.replace(/__name__/g, index++));
    } while($("#"+$newForm.find("[id]").attr("id")).length);
    $collection.data("index", index);
    $collection.append($newForm);
    addTagFormDeleteLink($newForm);

    $newForm.find("[type=file]").change( function(event) {
        let inputFile = event.currentTarget;
        $(inputFile).parent().find(".custom-file-label").html($(inputFile).val());
    });
}

$(".form-collection").each(function( index, collection ) {
    var $collection = $(collection);
    var collectionId = $collection.attr("id");

    var $elements = $collection.children();

    // $collection.empty();

    $collection.data("index",$elements.length);

    $elements.each(function(index, element) {
        var $element = $(element);
        addTagFormDeleteLink($element);
    });

    $("body").on("click", "[data-collection-holder="+collectionId+"]", function(e) {
        addFormToCollection($collection);
    });

    if(!$elements.length) {
        addFormToCollection($collection);
    }
});

$("[type=file]").each(function( index, input ) {
    var $input = $(input);
    $($input).next(".custom-file-label").html($input.val());
});

$(".custom-file-input").change( function(event) {
    let inputFile = event.currentTarget;
    $(inputFile).parent().find(".custom-file-label").html($(inputFile).val());
});
