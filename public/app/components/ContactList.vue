<template>
  <div>
    <table class="contacts-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Address</th>
          <th>Phones</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="contact in contacts" :key="contact.id">
          <td>{{ contact.name }}</td>
          <td>{{ contact.email }}</td>
          <td>{{ contact.address }}</td>
          <td>{{ contact.phones.map(p => p.phone).join(', ') }}</td>
          <td>
            <button @click="$emit('edit', contact)">Edit</button>
            <button @click="deleteContact(contact.id)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
    <div class="pagination">
      <button @click="prevPage" :disabled="page === 1">Previous</button>
      <span>Page {{ page }} of {{ totalPages }}</span>
      <button @click="nextPage" :disabled="page === totalPages">Next</button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return { contacts: [], page:1, perPage:10, total:0 };
  },
  computed: { totalPages() { return Math.ceil(this.total / this.perPage); } },
  methods: {
    fetchContacts() {
      fetch(`/app/api.php?method=list&page=${this.page}`)
      .then(res => res.json())
      .then(data => {
        if(data.status==='success') { this.contacts = data.data; this.total = data.total; }
      }).catch(err=>console.error(err));
    },
    nextPage() { if(this.page<this.totalPages){this.page++; this.fetchContacts();} },
    prevPage() { if(this.page>1){this.page--; this.fetchContacts();} },
    deleteContact(id) {
      if(!confirm('Are you sure?')) return;
      fetch(`/app/api.php?method=delete&id=${id}`, { method:'DELETE' })
        .then(res=>res.json())
        .then(data=>{
          if(data.status==='success'){ this.fetchContacts(); }
          else { alert('Failed to delete'); }
        }).catch(err=>console.error(err));
    }
  },
  mounted() { this.fetchContacts(); }
}
</script>
