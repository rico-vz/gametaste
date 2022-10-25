// Check if terms are accepted and input fields are filled
function checkFormInput(theForm) {
  if (theForm.uname.value == false) {
    toastr.error("You must pick your username");
    return false;
  }
  if (theForm.email.value == false) {
    toastr.error("You must enter your email adress");
    return false;
  }
  if (theForm.password.value == false) {
    toastr.error("You must pick a password");
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
  if (theForm.display_name.value == false) {
    toastr.error("You must enter your display name");
    return false;
  }
  if (theForm.answer.value == false) {
    toastr.error("You must answer the security question");
    return false;
  }
  if (theForm.uname.value.length >= 17) {
    toastr.error("Username must be less than 17 characters");
    return false;
  }
  if (theForm.display_name.value.length >= 17) {
    toastr.error("Display name must be less than 17 characters");
    return false;
  }
}
