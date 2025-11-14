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

    function maskPhone(input) {
        let v = input.value.replace(/\D/g, '');
        if (v.length > 10) {
            v = v.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
        } else if (v.length > 5) {
            v = v.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
        } else if (v.length > 2) {
            v = v.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
        } else {
            v = v.replace(/^(\d*)/, '($1');
        }
        input.value = v;
    }

    function validatePhone(input) {
        const pattern = /^\(\d{2}\) \d{4,5}-\d{4}$/;
        const errorMsg = input.nextElementSibling;
        if (!pattern.test(input.value)) {
            errorMsg.textContent = 'Formato inválido. Use (99) 99999-9999';
            input.style.borderColor = '#ff4d4d';
            return false;
        } else {
            errorMsg.textContent = '';
            input.style.borderColor = '#4caf50';
            return true;
        }
    }

    document.addEventListener('input', function(e) {
        if(e.target.name === 'phones[]') {
            maskPhone(e.target);
            validatePhone(e.target);
        }
    });

    document.getElementById('add-phone').addEventListener('click', function() {
        const container = document.getElementById('phones-container');
        const div = document.createElement('div');
        div.className = 'phone-field';
        div.innerHTML = `<input type="text" name="phones[]" placeholder="(99) 99999-9999" required>
                        <button type="button" class="remove-phone delete">Remove</button>
                        <span class="error-msg"></span>`;
        container.appendChild(div);
    });

    document.getElementById('phones-container').addEventListener('click', function(e) {
        if(e.target.classList.contains('remove-phone')){
            e.target.parentElement.remove();
        }
    });
});
