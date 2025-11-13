const { createApp } = Vue;

createApp({
    data() {
        return {
            contacts: [],
            newContact: { name: '', email: '', address: '', phones: [''] },
            editingContact: null,
            message: ''
        }
    },
    mounted() {
        this.fetchContacts();
    },
    methods: {
        async fetchContacts() {
            const res = await fetch('../api.php?method=list&page=1');
            const data = await res.json();
            if (data.status === 'success') {
                this.contacts = data.data;
            }
        },
        async saveContact() {
            const method = this.editingContact ? 'update' : 'create';
            const id = this.editingContact?.id;
            const url = `../api.php?method=${method}${id ? '&id=' + id : ''}`;

            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(this.newContact)
            });
            const result = await res.json();
            if (result.status === 'success') {
                this.message = 'Contact saved!';
                this.fetchContacts();
                this.newContact = { name: '', email: '', address: '', phones: [''] };
                this.editingContact = null;
            } else {
                this.message = result.message || 'Error saving contact';
            }
        },
        editContact(contact) {
            this.editingContact = contact;
            this.newContact = JSON.parse(JSON.stringify(contact));
        },
        async deleteContact(contact) {
            if (!confirm('Delete this contact?')) return;
            const res = await fetch(`../api.php?method=delete&id=${contact.id}`, { method: 'DELETE' });
            const result = await res.json();
            if (result.status === 'success') {
                this.message = 'Contact deleted!';
                this.fetchContacts();
            }
        },
        addPhoneField() {
            this.newContact.phones.push('');
        },
        removePhoneField(index) {
            this.newContact.phones.splice(index, 1);
        }
    }
}).mount('#app');
