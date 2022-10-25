// Check if terms are accepted and input fields are filled
function checkFormInput(theForm) {
    if (theForm.password.value == false) {
        toastr.error("You must enter a password");
        return false;
    }
    if (theForm.passwordRepeat.value == false) {
        toastr.error("You must repeat your password");
        return false;
    }
    if (theForm.password.value != theForm.passwordRepeat.value) {
        toastr.error("Passwords do not match");
        return false;
    }
}