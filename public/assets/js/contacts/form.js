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

});
