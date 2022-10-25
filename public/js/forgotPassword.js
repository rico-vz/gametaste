// Check if terms are accepted and input fields are filled
function checkFormInput(theForm) {
    if (theForm.username.value == false) {
        toastr.error("You must enter your username");
        return false;
    }
    if (theForm.email.value == false) {
        toastr.error("You must enter your email adress");
        return false;
    }
    if (theForm.answer.value == false) {
        toastr.error("You must answer the security question");
        return false;
    }
}