document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('add-phone').addEventListener('click', function() {
        const container = document.getElementById('phones-container');
        const div = document.createElement('div');
        div.className = 'phone-field';
        div.innerHTML = '<input type="text" name="phones[]" value=""> <button type="button" class="remove-phone">Remove</button>';
        container.appendChild(div);
    });

    document.getElementById('phones-container').addEventListener('click', function(e) {
        if (e.target && e.target.className === 'remove-phone') {
            e.target.parentNode.remove();
        }
    });
});
