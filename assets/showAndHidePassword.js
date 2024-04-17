const eyeOn = document.querySelector('.eye-on');
const eyeOff = document.querySelector('.eye-off');
const inputPassword = document.querySelector('#inputPassword');

// $('.eyeOn').on('click', function(e){
//     console.log('cliock');
// });
eyeOff.style.display = "none";

// Display password
eyeOn.addEventListener('click', () => {
    // $('.eyeOn').addClass('d-none');
    // $('.eyeOff').addClass('d-block');
    // // $('#inputPassword').
    eyeOn.style.display = "none";
    eyeOff.style.display = "block";
    inputPassword.type = "text";
})

//Hide password
eyeOff.addEventListener('click', () => {
    eyeOff.style.display = "none";
    eyeOn.style.display = "block";
    inputPassword.type = "password";
})