<template>
  <div class="form-container">
    <div v-if="successMessage" class="message success">{{ successMessage }}</div>
    <div v-if="errorMessage" class="message error">{{ errorMessage }}</div>

    <h2>{{ form.id ? 'Edit Contact' : 'Add Contact' }}</h2>
    <form @submit.prevent="saveContact">
      <input type="hidden" v-model="form.id">
      <label>Name:</label>
      <input type="text" v-model="form.name" required>

      <label>Email:</label>
      <input type="email" v-model="form.email" required>

      <label>Address:</label>
      <input type="text" v-model="form.address">

      <label>Phones:</label>
      <div v-for="(phone, index) in form.phones" :key="index" class="phone-field">
        <input type="text" v-model="form.phones[index]">
        <button type="button" @click="removePhone(index)">Remove</button>
      </div>
      <button type="button" @click="addPhone">Add Phone</button>

      <button type="submit">{{ form.id ? 'Update' : 'Save' }}</button>
      <button type="button" @click="resetForm">Reset</button>
    </form>
  </div>
</template>

<script>
export default {
  data() {
    return {
      form: { id:'', name:'', email:'', address:'', phones:[''] },
      successMessage: '',
      errorMessage: ''
    };
  },
  methods: {
    addPhone() { this.form.phones.push(''); },
    removePhone(i) { this.form.phones.splice(i, 1); },
    resetForm() { 
      this.form = { id:'', name:'', email:'', address:'', phones:[''] }; 
      this.successMessage = ''; this.errorMessage = ''; 
    },
    saveContact() {
      this.errorMessage = ''; this.successMessage = '';
      const method = this.form.id ? 'update' : 'create';
      const idQuery = this.form.id ? '&id=' + this.form.id : '';
      fetch(`/app/api.php?method=${method}${idQuery}`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(this.form)
      })
      .then(res => res.json())
      .then(data => {
        if(data.status === 'success') {
          this.successMessage = data.message || 'Contact saved!';
          this.resetForm();
          this.$emit('saved');
        } else {
          this.errorMessage = data.message || 'Failed to save contact.';
        }
      })
      .catch(err => { this.errorMessage = 'Error connecting to API.'; console.error(err); });
    }
  }
}
</script>
