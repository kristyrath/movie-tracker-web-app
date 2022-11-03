

"use strict";




window.addEventListener("DOMContentLoaded", () => {



    // GET INPUT FROM DOM 
    const registerForm = document.querySelector("form");
    const username = document.getElementById("username");
    const fname = document.getElementById("firstName");
    const lname = document.getElementById("lastName");
    const pass = document.getElementById("password");
    const email = document.getElementById("email");
    const confirmEmail = document.getElementById("confirm-email");


    // SET ERROR BOOLEANS TO FALSE
    let emailValid = false;
    let usernameValid = false;
    let fnameValid = false;
    let lnameValid = false; 
    let confirmEmailValid = false;
    let passValid = false;
    

    // VARIABLES OF PASSWORD STRENGHT INDICATOR
    var length = document.getElementById('length');
    var lowercase = document.getElementById('lowercase');
    var uppercase = document.getElementById('uppercase');
    var number = document.getElementById('number');
    var special = document.getElementById('special');
    
    // VALIDATE EMAIL
    email.addEventListener("blur", function() {
        // remove preivous errors if it exists
        const emailError1 = document.getElementById("emailError1");
        const emailError2 = document.getElementById("emailError2");

        if (emailError1) {
            emailError1.remove();
        }
        if (emailError2) {
            emailError2.remove();
        }

        // get request
        if (email.value) { 
            const xhr = new XMLHttpRequest();
            // calls sql query to check if email is unique
            const url = `checkemail.php?email=${email.value}`;
            xhr.open("GET", url);
            xhr.addEventListener("load", (ev) => {
                if (xhr.status == 200) {
                    // inserts error message if email is not unique or is invalid
                    if (xhr.response == "true") {
                        email.insertAdjacentHTML("afterend",`<span class="error" id="emailError1">An account already exists with this email.</span>`); 
                        emailValid = false;
                    
                    }
                    else if  (xhr.response == "error") {
                        email.insertAdjacentHTML("afterend",`<span class="error" id="emailError2">Please enter a valid email address.</span>`); 
                        emailValid = false;
                    }
                    else { 
                        emailValid = true;
                    }

                }
                else {
                    console.log("Error. Check status.");
                    emailValid = false;  
                }


            });
            xhr.send();

        }
        
    });
    // VALIDATE USERNAME
    username.addEventListener("blur", function() {
        const userNameError = document.getElementById("userNameError");

        if (userNameError) {
            userNameError.remove();
        }
        if (username.value) {
            const xhr = new XMLHttpRequest();
            // calls a php query to check if username is unique
            const url = `checkusername.php?username=${username.value}`;
            xhr.open("GET", url);
            xhr.addEventListener("load", (ev) => {
                if (xhr.status == 200) {
                    // insert appropriate error message in DOM
                    if (xhr.response == "true") {
                        username.insertAdjacentHTML("afterend",`<span class="error" id="userNameError">Username is taken.</span>`);
                        usernameValid = false; 
                    }
                    else if (!usernameIsValid(username)) {
                        username.insertAdjacentHTML("afterend",`<span class="error" id="userNameError">Please enter a valid username between 4-20 characters.</span>`);
                        usernameValid = false; 
                    }
                    else { 
                        usernameValid = true;
                    }
                    
                }
                else {
                    usernameValid = false;
                }

            });

            xhr.send();

        }

    });

    // VALIDATE CONFIRM EMAIL
    confirmEmail.addEventListener("blur", function() {
        const confirmEmailError = document.getElementById("confirmEmailError");
        if (confirmEmailError) {
            confirmEmailError.remove();
        }
        const email = document.getElementById("email");
        if (email.value && confirmEmail.value) {
            // calls function to check if email is inputted and if it is valid
            if (!confirmEmailIsValid(email, confirmEmail)) {
                // inserts appropriate error message
                confirmEmailValid = false;
                if (confirmEmail.value) {
                    confirmEmail.insertAdjacentHTML("afterend",`<span class="error" id="confirmEmailError">Email does not match. </span>`);   
                }
                
            }
            else { 
                confirmEmailValid = true;
            }
        }

    });

    // VALIDATE FIRST NAME
    fname.addEventListener("blur", function() {
        const firstNameError = document.getElementById("firstNameError");

        if (firstNameError) {
            firstNameError.remove();
        }
        if (fname.value) {
            // check if first name is valid without characters
            if (!nameIsValid(fname)) {
                fnameValid = false;
                fname.insertAdjacentHTML("afterend",`<span class="error" id="firstNameError">Please enter a valid first name. </span>`);   
            }
            else {
                fnameValid = true;
            }
        }

    });

    
    // VALIDATE LAST NAME
    lname.addEventListener("blur", () => {
        const lastNameError = document.getElementById("lastNameError");
        if (lastNameError) {
            lastNameError.remove();
        }
        if (lname.value) {
            // check if first name is valid without characters
            if (!nameIsValid(lname)) {
                lnameValid = false;
                lname.insertAdjacentHTML("afterend",`<span class="error" id="lastNameError">Please enter a valid last name. </span>`);   
            }
            else {
                lnameValid = true;
            }
        }
        
    });

    // VALIDATE PASSWORD
    const strengthField = document.getElementById("strengthField");
    // show pass strength indicate on focus
    pass.addEventListener("focus", function() {
        strengthField.classList.remove("hidden");
    });
    // PASSWORD STRENGHT INDICATOR - highlights requirements that are met
// THIS PORTION OF CODE IS SOURCED FROM  https://www.jqueryscript.net/blog/best-password-strength-checker.html#vanilla<https://www.jqueryscript.net/blog/best-password-strength-checker.html#vanilla
    // update pass strength indicator as user types
    pass.addEventListener("input", () => {
        checkLength(pass.value) ? length.classList.add("acquired") : length.classList.remove("acquired");
        checkIfOneLowercase(pass.value) ? lowercase.classList.add("acquired") : lowercase.classList.remove("acquired");
        checkIfOneUppercase(pass.value) ? uppercase.classList.add("acquired") : uppercase.classList.remove("acquired");
        checkIfOneDigit(pass.value) ? number.classList.add("acquired") : number.classList.remove("acquired");
        checkIfOneSpecialChar(pass.value) ? special.classList.add("acquired") : special.classList.remove("acquired");

    });
    // update error message on leave
    pass.addEventListener("blur", () => {
        const passwordError = document.getElementById("passwordError");
        if (passwordError) {
            passwordError.remove();
        }
        if (pass.value) {
            if (!passIsValid(pass)) {
                passValid = false;
                pass.insertAdjacentHTML("afterend",`<span class="error" id="passwordError">Please enter a valid password. </span>`);  
                 
            }
            else { 
                passValid = true;
            }
        }
        strengthField.classList.add("hidden");
    });

    // VALIDATE FORM BEFORE SUBMISSION
    registerForm.addEventListener("submit", (ev) => {
        const submitButton = document.getElementsByName("submit");
        if (!(passValid && usernameValid && fnameValid && lnameValid && emailValid && confirmEmailValid)) {
            ev.preventDefault();

        }

    })
});
// VALIDATES CONFIRM EMAIL ADDRESS
function confirmEmailIsValid(email, confirmEmail) {
    // checks if both versions of emails are equal
    if (email.value === confirmEmail.value) {
        return true;
    }
    return false;
}


// VALIDATE NAME TO ONLY CONTAIN ALPHABETS
function nameIsValid (name) {


    if (!/[^a-zA-Z]/.test(name.value) && name.value.length != 0) {
        return true;
    }
    return false;
    
}
// VALIDATE THE LENGTH OF USERNAME
function usernameIsValid(uname) {
    if (uname.value.length >= 4 && uname.value.length <=20) {
        return true;
    }
    return false;
}

// VALIDATE ALL PASS WORD CONDITIONS USING BELLOW METHODS
function passIsValid(pass) {
    if (checkLength(pass.value) && checkIfOneLowercase(pass.value) && checkIfOneSpecialChar(pass.value)
        && checkIfOneUppercase(pass.value) && checkIfOneDigit(pass.value)) {
        return true;
    }
    else {
        return false;
    }
}

// FUNCTIONS FOR VALIDATING SPECIFIC PASSWORD CONDITIONS
// sourced from https://www.cssscript.com/check-strength-passwords-pwstrength/
function checkLength(text){
    return text.length >= 9 && text.length <= 20;
}

function checkIfOneLowercase(text) {
    return /[a-z]/.test(text);
}

function checkIfOneUppercase(text) {
    return /[A-Z]/.test(text);
}

function checkIfOneDigit(text) {
    return /[0-9]/.test(text);
}

function checkIfOneSpecialChar(text) {
    return /[~`!#$%\^&*+=\-\[\]\\';,/{}|\\":<>\?]/g.test(text);
}



// medium when meets all requirement
// strong when more than 13 characters long, more than one upper case, lowercase, number and special char

// TO DO 
function passwordStrengthChecker(pass) {

}