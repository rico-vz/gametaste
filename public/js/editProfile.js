function checkFormInput(theForm) {
    if (theForm.avatar.value == false) {
        toastr.error("You must choose an avatar");
        return false;
    }
}