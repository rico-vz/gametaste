// Check if terms are accepted and input fields are filled
function checkFormInput(theForm) {
    if (theForm.reviewTitle.value == false) {
        toastr.error("You must enter a title for your review");
        return false;
    }
    if (theForm.rating.value == false) {
        toastr.error("You must rate the game");
        return false;
    }
    if (theForm.reviewText.value == false) {
        toastr.error("You must enter a review");
        return false;
    }
    if (theForm.termsOfService.checked == false) {
        toastr.error('You must agree with the Terms of Service!');
        return false;
    }
}