
function deleteMessage(actionLink) {
    if(confirm('Confirmez-vous la suppression?')) {
        $('#deleteTrick').attr('action', actionLink).submit();
    }
}

jQuery(document).ready(function() {
    // Get the ul that holds the collection of tags
    var $tagsCollectionHolder = $('ul.medias');

    if($tagsCollectionHolder.length) {
        // count the current form inputs we have (e.g. 2), use that as the new
        // index when inserting a new item (e.g. 2)
        $tagsCollectionHolder.data('index', $tagsCollectionHolder.find(':input').length);

        // add a delete link to all of the existing tag form li elements
        $tagsCollectionHolder.find('li').each(function() {
            addTagFormDeleteLink($(this));
        });

        addFormToCollection('medias');
    }

    $('body').on('click', '.add_item_link', function(e) {
        var $collectionHolderClass = $(e.currentTarget).data('collectionHolderClass');
        // add a new tag form (see next code block)
        addFormToCollection($collectionHolderClass);
    })

    var discussionMax = 5;
    var $messageCollection = $('#discussions .discussion-message');
    if($messageCollection.length > discussionMax) {
        var currentLoaded = discussionMax;
        for (var i = discussionMax; i < $messageCollection.length; i++) {
            $($messageCollection.get(i)).hide();
        }
        var $extendButton = $('<button class="btn btn-primary btn-lg active" type="button">Charger plus</button>');
        $extendButton.on('click', function(e) {
            for (var i = currentLoaded; i < currentLoaded+discussionMax; i++) {
                current = $messageCollection.get(i);
                if(!current)
                    break;
                $(current).show();
            }
            currentLoaded+=discussionMax;
            if(currentLoaded>=$messageCollection.length)
                this.remove();
        });
        $('#discussions').append($extendButton);
    }
});

function addFormToCollection($collectionHolderClass) {
    // Get the ul that holds the collection of tags
    var $collectionHolder = $('.' + $collectionHolderClass);

    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    var newForm = prototype;
    // You need this only if you didn't set 'label' => false in your tags field in TaskType
    // Replace '__name__label__' in the prototype's HTML to
    // instead be a number based on how many items we have
    // newForm = newForm.replace(/__name__label__/g, index);

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    newForm = newForm.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    var $newFormLi = $('<li></li>').append(newForm);
    // Add the new form at the end of the list
    $collectionHolder.append($newFormLi)

    // add a delete link to the new form
    addTagFormDeleteLink($newFormLi);
}

function addTagFormDeleteLink($tagFormLi) {
    var $removeFormButton = $('<button class="btn btn-warning" type="button">Supprimer le champ ci-dessus</button>');
    $tagFormLi.append($removeFormButton);

    $removeFormButton.on('click', function(e) {
        // remove the li for the tag form
        $tagFormLi.remove();
    });
}
