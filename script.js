function hideableInputs() {
    const allCheckboxes = document.querySelectorAll('input[type="checkbox"]');

    allCheckboxes.forEach(function(checkbox) {
        const textInput = checkbox.parentElement.parentElement.querySelector('input[type="text"');
        
        if (textInput) {
            if (checkbox.checked) textInput.classList.remove('greyedOut'); // init

            checkbox.addEventListener('change', function() {
                textInput.classList.toggle('greyedOut');
            });
        }
    })
}

window.addEventListener('DOMContentLoaded', () => {
    hideableInputs()

    const emailString = document.querySelector('#email_string');
    if (emailString) { // prevents from adding spaces in email username 
        emailString.addEventListener("keyup", function() {
            this.value = this.value.replace(/[^A-Za-z]/g,'');
        });
    }
});