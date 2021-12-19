<template>
  <div id="signin">
    <div class="signin-form">
      <form @submit.prevent>
          
       <div class="input hobbies">
            <button class="btn btn-default"  @click="buildProject()">Build project</button>
         
       </div>
      <div class="input hobbies">
            <button class="btn btn-default"  @click="downloadProject()">Download Project</button>
         
       </div>

      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
  export default {
      data () {
      return {
        "zip_path" : ''
      }
    },
    methods: {
      buildProject() {
        let formData = {
            'programming_language' : 'laravel'
        }
        axios.post('http://localhost:9000/build_project/' + id ,formData)
        .then(function(response){
            this.zip_path = response.zip_link;
            alert( "Project built successfully!" );

        } )
        .catch(error => alert( "Error building the project !" ));

      },

       
    
      downloadProject () {
         let formData = {
             'zip_path' : this.zip_link
         };
        axios.post('http://localhost:9000/download_zip',formData)
        .then(function(response){
            alert( "Project Downloaded successfully!" );
        } )
        .catch(error => alert( "Error downloading the project !" ));
      }

    }
    
  }
</script>

<style scoped>
  .signin-form {
    width: 450px;
    margin: 50px auto;
    border: 1px solid #eee;
    padding: 60px;
    box-shadow: 0 2px 3px #ccc;
  }

  .input {
    margin: 15px auto;
  }

  .input label {
    display: block;
    color: #4e4e4e;
    margin-bottom: 5px;
  }

  .input input {
    font: inherit;
    width: 100%;
    padding: 6px 12px;
    box-sizing: border-box;
    border: 1px solid #ccc;
  }

  .input input:focus {
    outline: none;
    border: 1px solid #521751;
    background-color: #eee;
  }

  .input select {
    font: inherit;
    width: 100%;
    padding: 6px 12px;
    box-sizing: border-box;
    border: 1px solid #ccc;
  }
  
  .input select:focus {
    outline: none;
    border: 1px solid #521751;
    background-color: #eee;
  }
  

  .submit button {
    border: 1px solid #521751;
    color: #521751;
    padding: 10px 20px;
    font: inherit;
    cursor: pointer;
  }

  .submit button:hover,
  .submit button:active {
    background-color: #521751;
    color: white;
  }

  .submit button[disabled],
  .submit button[disabled]:hover,
  .submit button[disabled]:active {
    border: 1px solid #ccc;
    background-color: transparent;
    color: #ccc;
    cursor: not-allowed;
  }
  
  .hobbies button {
    border: 1px solid #521751;
    background: #521751;
    color: white;
    padding: 6px;
    font: inherit;
    cursor: pointer;
  }

  .hobbies button:hover,
  .hobbies button:active {
    background-color: #8d4288;
  }

  .hobbies input {
    width: 90%;
  }
  .hobbies input {
    width: 90%;
  }

</style>