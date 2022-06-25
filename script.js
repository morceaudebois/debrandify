function hideableInputs() {
    const allCheckboxes = document.querySelectorAll('input[type="checkbox"]');

    allCheckboxes.forEach(function(checkbox) {
        const textInput = checkbox.parentElement.querySelector('input[type="text"');
        
        if (textInput) {
            if (checkbox.checked) textInput.style.display = 'none'; // init

            checkbox.addEventListener('change', function() {
                textInput.style.display = this.checked ? 'none' : 'inline';
            });
        }
    })
}

window.addEventListener('DOMContentLoaded', () => {
    hideableInputs()
});