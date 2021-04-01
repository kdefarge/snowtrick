function deleteMessage(actionLink) {
    if(confirm("Confirmez-vous la suppression?")) {
        $("#deleteTrick").attr("action", actionLink).submit();
    }
}
