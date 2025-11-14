document.addEventListener("DOMContentLoaded", () => {

    const btnAddPhone = document.getElementById("add-phone");
    const phonesContainer = document.getElementById("phones-container");

    // Add a new phone input field
    btnAddPhone.addEventListener("click", () => {
        const field = document.createElement("div");
        field.classList.add("phone-field");

        field.innerHTML = `
            <input type="text" name="phones[]" value="">
            <button type="button" class="remove-phone">Remove</button>
        `;

        phonesContainer.appendChild(field);
    });

    // Remove a phone input field
    phonesContainer.addEventListener("click", (e) => {
        if (e.target.classList.contains("remove-phone")) {
            e.target.parentElement.remove();
        }
    });

    /**
    * Applies a Brazilian phone mask in real-time while the user types.
    * Supported formats:
    * - (XX) XXXXX-XXXX → mobile
    * - (XX) XXXX-XXXX  → landline
    * 
    * @param {HTMLInputElement} input - The input field to apply the mask to.
    */
    function maskPhone(input) {
        // Remove all non-digit characters
        let v = input.value.replace(/\D/g, '');

        // Format for 11-digit numbers (mobile with area code)
        if (v.length > 10) {
            v = v.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
        } 
        // Format for 6-10 digits (landline with area code or incomplete mobile)
        else if (v.length > 5) {
            v = v.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
        } 
        // Partial formatting after typing area code
        else if (v.length > 2) {
            v = v.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
        } 
        // Only open parentheses for area code
        else {
            v = v.replace(/^(\d*)/, '($1');
        }

        // Update input value with the applied mask
        input.value = v;
    }

    /**
    * Validates if the phone input matches the correct format.
    * Valid formats: (XX) XXXX-XXXX or (XX) XXXXX-XXXX
    * 
    * @param {HTMLInputElement} input - The input field to validate.
    * @returns {boolean} true if valid, false otherwise
    */
    function validatePhone(input) {
        const pattern = /^\(\d{2}\) \d{4,5}-\d{4}$/; // Regex for validation
        const errorMsg = input.nextElementSibling;   // <span> element for error messages

        if (!pattern.test(input.value)) {
            errorMsg.textContent = 'Invalid format. Use (99) 99999-9999';
            input.style.borderColor = '#ff4d4d'; // red indicates error
            return false;
        } else {
            errorMsg.textContent = '';
            input.style.borderColor = '#4caf50'; // green indicates valid
            return true;
        }
    }

    /**
    * Automatically applies mask and validation to any phone input
    * whenever the user types.
    */
    document.addEventListener('input', function(e) {
        if (e.target.name === 'phones[]') {
            maskPhone(e.target);
            validatePhone(e.target);
        }
    });

    /**
    * Allows dynamically adding new phone fields.
    * Each field includes an input, a remove button, and a span for error messages.
    */
    document.getElementById('add-phone').addEventListener('click', function() {
        const container = document.getElementById('phones-container');
        const div = document.createElement('div');
        div.className = 'phone-field';
        div.innerHTML = `
            <input type="text" name="phones[]" placeholder="(99) 99999-9999" required>
            <button type="button" class="remove-phone delete">Remove</button>
            <span class="error-msg"></span>
        `;
        container.appendChild(div);
    });

    /**
    * Allows removing any dynamically added phone field.
    * Listens for click events inside the container and checks if
    * the clicked button has the 'remove-phone' class.
    */
    document.getElementById('phones-container').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-phone')) {
            e.target.parentElement.remove();
        }
    });
});
