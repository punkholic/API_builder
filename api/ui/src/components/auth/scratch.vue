<template>
  <div id="signin">
    <div class="signin-form">
      <form @submit.prevent>
          <h5 class="text-center">Configuration setup</h5>
    <hr />
        <div class="input">
          <label for="appName">App Name</label>
         <input
            type="text"
            class="form-control"
            v-model="appName"
            required
            id=""
            placeholder="application name"
          />
        </div>
        <div class="input">
           <label for="planguage"
          >Programming language</label
        >
          <select
            class="form-control"
            v-model="programmingLanguage"
            required
          >
            <option value="laravel">Laravel</option>
            <option value="codeiginator">Codeiginator</option>
          </select>
        </div>
         <div class="input">
          <label for="databaseName">Database Name</label>
         <input
            type="text"
            class="form-control"
            v-model="databaseName"
            required
            id=""
            placeholder="Database name"
          />
        </div>
        <div class="input">
          <label for="databaseUsername">Database Username</label>
         <input
            type="text"
            class="form-control"
            v-model="databaseUsername"
            required
            id=""
            placeholder=" Enter Database Username(eg: root)"
          />
        </div>
        <div class="input">
          <label for="databasePassword">Database Password</label>
         <input
            type="text"
            class="form-control"
            v-model="databasePassword"
            required
            id=""
            placeholder="Database Password (eg: root)"
          />
        </div>

        <div class="input">
        <label for="mode">Mode</label>
          <button class="btn btn-default disabled">Development</button>

        </div>

         
     <div class="modelset">
        <h5 class="text-center">Model Setup</h5>
      <hr />
       <div class="input">
         <label for="modelName">Model data</label>
             <input
              type="text"
              required
              class="form-control"
              v-model="form.modelData"
              name="model-name"
              id="model-name"
            />
             <p>
              <small
                ><strong>( Format :tablename:field1,field2,...,fieldn;)</strong></small
              >
            </p>
       </div>
       <div class="input hobbies">
            <button class="btn btn-default"  @click="addModel()">save</button>
         
       </div>
               <div class="show" v-for="(m, i) in model" :key="i">
          <div class="input ">
            <label for="id" class="col-sm-4 control-label">{{ m }}</label>
            <div>
              <select v-model="selected[m]" class="form-control">
                <option value="primary" >primary</option>
                <option value="string">String</option>
                <option value="integer">Integer</option>
                <option value="text">Text</option>
                <option value="hash">Hash</option>
                <option value="enum">Enum</option>
                <option value="date">Date</option>
                <option value="decimal">Decimal</option>
                <option value="datetime">DataTime</option>




              </select>
           
            </div>
                 
          </div>

                  
        </div>

     </div>
       <div class="input">
         <label for="fillableFields">Fillable For Model</label>
             <input
              type="text"
              required
              class="form-control"
              v-model="form.fillableFields"
              name="fillableFields"
              id="fillableFields"
            />
             <p>
              <small
                ><strong>( Format :field1,field2,...,fieldn;)</strong></small
              >
            </p>
       </div>
       <div class="input">
         <label for="guarded_fields">Guarded Fields For Model</label>
             <input
              type="text"
              required
              class="form-control"
              v-model="form.guarded_fields"
              name="guarded_fields"
              id="guarded_fields"
            />
             <p>
              <small
                ><strong>( Format :field1,field2,...,fieldn;)</strong></small
              >
            </p>
       </div>
       <div class="checkbox">
         <label for="timestamp">Create Timestamp</label>
             <input
              type="checkbox"              
              v-model="form.timestamp"
              name="timestamp"
              id="timestamp"
            />
            
       </div>
      <div class="input hobbies">
            <button class="btn btn-default"  @click="saveData()">save data</button>
         
       </div>
        
       
        <div class="submit">
          <button @click="onSubmit">Submit</button>
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
              appName:'',
              programmingLanguage:'',
              databaseName:'',
              databaseUsername:'',
              databasePassword:'',
              form:{},
              model: {},
              selected: [],
              // data_setup:[{}],
              checkedNames:[],
              data:[],
              


       
      }
    },
    methods: {
      saveData() {
            let Guarded = this.form.guarded_fields;
            let guarded_fields_needed = Guarded.split(",");
            let fillable_field = this.form.fillableFields;
            let fillable_field_needed = fillable_field.split(",");
            let timestamp = this.form.timestamp;
 
            
           let data_payload = {
             tableName:this.tableName,
             controller: this.tableName + "Controller",

             model: {
               fields: this.model = this.selected,  
               guarded: guarded_fields_needed,         
               fillable: fillable_field_needed,
               mapping: [],
               timestamps: timestamp,
               view: [],
               add: [],
               edit: [],
               delete: []
             },
            
          };
        
        this.data.push( data_payload );
      },
       addModel(){
      let models = this.form.modelData;
      this.xyz = models.split(":");
      this.tableName = this.xyz[0];
      this.model = this.xyz[1].split(",");
     
     
    },
    
      onSubmit () {
       
         let config = {
           app_name : this.appName,
          programming_langauge: this.programmingLanguage,
          mode: 'development',
          database_name:this.databaseName,
          database_username:this.databaseUsername,
          database_password:this.databasePassword,
         };
        

        
        let formData = {

          config:config,
  
          data: this.data

        };

        let id = (this.random = Math.floor(Math.random() * 1000000000) + 1);
       

       
  // console.log(formData);
  // return;
        axios.post('http://localhost:9000/save/'+id ,formData)
        .then(res => console.log(res) )
        .catch(error => console.log(error));

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