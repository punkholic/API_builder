<template>
  <div id="dashboard">
    <h1>That's the dashboard!</h1>
    
  </div>
</template>

<script>
import axios from 'axios';
export default {
  created() {
    axios.get('https://api-builder-2b966-default-rtdb.firebaseio.com/users.json')
    .then(res => console.log(res))
    .catch(error => console.log(error))

  }
}
</script>

<style scoped>
  h1, p {
    text-align: center;
  }

  p {
    color: red;
  }
</style>