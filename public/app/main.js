const { createApp } = Vue;

createApp({
    data() {
        return {
            // List of contacts loaded from the API
            contacts: [],

            // Form model used for both creating and editing a contact
            newContact: { name: '', email: '', address: '', phones: [''] },

            // Stores the contact currently being edited (null means "create mode")
            editingContact: null,

            // Generic feedback message displayed to the user
            message: ''
        };
    },

    /**
     * Lifecycle hook that triggers automatically when the component
     * is mounted on the DOM. Used here to load contacts from the API.
     */
    mounted() {
        this.fetchContacts();
    },

    methods: {
        /**
         * Fetch all contacts from the API.
         * This function loads the first page only for simplicity.
         */
        async fetchContacts() {
            const res = await fetch('../api.php?method=list&page=1');
            const data = await res.json();

            if (data.status === 'success') {
                // Update local list with server data
                this.contacts = data.data;
            }
        },

        /**
         * Save a contact.
         * Determines whether to create a new record or update an existing one.
         */
        async saveContact() {
            // Determine the correct API method (create or update)
            const method = this.editingContact ? 'update' : 'create';
            const id = this.editingContact?.id;

            // Generate the URL with ID only in update mode
            const url = `../api.php?method=${method}${id ? '&id=' + id : ''}`;

            // Send contact data as JSON
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(this.newContact)
            });

            const result = await res.json();

            if (result.status === 'success') {
                this.message = 'Contact saved!';

                // Reload contact list
                this.fetchContacts();

                // Reset form
                this.newContact = { name: '', email: '', address: '', phones: [''] };
                this.editingContact = null;
            } else {
                this.message = result.message || 'Error saving contact';
            }
        },

        /**
         * Load a contact into the form for editing.
         * Uses deep cloning to avoid mutating the source object directly.
         */
        editContact(contact) {
            this.editingContact = contact;

            // Deep clone to avoid changing original object while typing
            this.newContact = JSON.parse(JSON.stringify(contact));
        },

        /**
         * Delete a contact after user confirmation.
         */
        async deleteContact(contact) {
            if (!confirm('Delete this contact?')) return;

            const res = await fetch(
                `../api.php?method=delete&id=${contact.id}`,
                { method: 'DELETE' }
            );

            const result = await res.json();

            if (result.status === 'success') {
                this.message = 'Contact deleted!';

                // Reload contacts after deletion
                this.fetchContacts();
            }
        },

        /**
         * Add a new empty phone field to the form.
         */
        addPhoneField() {
            this.newContact.phones.push('');
        },

        /**
         * Remove a phone field by its array index.
         */
        removePhoneField(index) {
            this.newContact.phones.splice(index, 1);
        }
    }
}).mount('#app');
