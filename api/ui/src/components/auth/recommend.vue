<template>
  <div id="signin">
    <div class="signin-form" v-show="!this.visible">
      <form @submit.prevent>
        <h5 class="text-center">Configuration setup</h5>
        <hr />
        <div class="input">
          <label for="appName">App Name</label>
          <input
            type="text"
            class="form-control"
            v-model="appName"
            id=""
            placeholder="application name"
          />
        </div>
        <div class="input">
          <label for="planguage">Programming language</label>
          <select class="form-control" v-model="programmingLanguage">
            <option value="laravel" selected>Laravel</option>
            <option value="codeigniter">Codeigniter</option>
          </select>
        </div>

        <h5 class="text-center">Model Setup</h5>
        <hr />

        <div class="input">
          <label for="models">Select Model</label>

          <select
            id="models"
            @change="renderForm()"
            class="form-control"
            v-model="form.modelId"
          >
            <option value="-1" selected>New Model</option>
          </select>
          <br />
          <label for="table_name">Table Name</label>
          <input
            type="text"
            class="form-control"
            v-model="form.table_name"
            name="table_name"
            id="table_name"
            value="Table Name"
          />
          <p>
            <small><strong>( Format : teacher)</strong></small>
          </p>

          <label for="model_fields">Fields</label>
          <div id="toAdd">
            <div id="toReplica">
              <input
                type="text"
                class="form-control"
                name="fields_test"
                id="fields_test"
              />
            </div>
          </div>
          <button @click="replica()">Add More</button>
        </div>
        <div class="input hobbies">
          <button class="btn btn-default" @click="saveData()">
            Add new model
          </button>
        </div>
        <div class="submit">
          <button @click="onSubmit">Recommend</button>
        </div>
        <div class="input hobbies">
          <button
            class="btn btn-default"
            id="download_btn"
            @click="downloadProject()"
            style="display: none"
          >
            Download Recommended project
          </button>
        </div>
      </form>
    </div>
    <img
      src="https://i.gifer.com/4V0b.gif"
      class="image"
      alt=""
      v-fi="visible"
      v-show="this.visible"
    />
  </div>
</template>

<script>
import axios from "axios";
export default {
  data() {
    return {
      visible: false,
      appName: "Blog",
      programmingLanguage: "laravel",
      form: {
        table_name: "Abc",
        model_fields: "",
      },
      model: {},
      // selected: [],
      checkedNames: [],
      data: [],
      formData: [],
    };
  },
  methods: {
    clearForm() {
      this.model_fields = "";
      this.form.modelData = "";

      this.form.table_name = "";
      this.form.view_fields = "";
      this.form.view_request_route = "";
      this.form.view_request_name = "";

      let toReplica = document.querySelector("#toReplica");
      toReplica.querySelector("input[type='text']").value = "";

      let toRemove =
        toReplica.parentElement.parentElement.querySelectorAll("#toReplica");

      for (let i = 0; i < toRemove.length; i++) {
        if (i != 0) {
          toRemove[i].parentNode.removeChild(toRemove[i]);
        }
      }
    },
    renderForm() {
      let modelId = document.querySelector("#models").selectedIndex - 1;
      if (modelId != -1) {
        this.form = this.formData[modelId];
        let fields = this.data[modelId].db_fields;
        document.querySelector("#table_name").textContent =
          this.data[modelId].tableName;
        let index = 0;
        fields.forEach((e) => {
          if (index == 1) {
            this.replica();
          }

          let element = document.querySelectorAll("#toReplica")[index];
          index = 1;
          element.querySelector("input[type='text']").value = e;
        });
      } else {
        this.clearForm();
      }
    },
    replica() {
      let toReplica = document.querySelector("#toReplica");
      let toAdd = document.querySelector("#toAdd");
      toAdd.insertAdjacentHTML("afterend", toReplica.outerHTML);
    },

    saveData() {
      let fields_p_load = [];
      let replicatedArray = document.querySelectorAll("#toReplica");

      let tableName = this.form.table_name;

      replicatedArray.forEach((element) => {
        let field_name = element.querySelector("input[type='text']").value;
        fields_p_load.push(field_name);
      });

      let data_payload = {
        tableName: tableName,
        db_fields: fields_p_load,
      };

      this.data.push(data_payload);

      /**
       * Unsetting all The fields after the data are pushed in the global array
       */

      let models = document.querySelector("#models");

      if (models.value == -1 || models.value == "") {
        let option = document.createElement("option");
        option.textContent = `Model ${this.data.length}`;
        option.setAttribute("value", this.data.length - 1);
        models.appendChild(option);
        this.formData.push(Object.assign({}, this.form));
      } else {
        this.formData[this.form.modelId] = Object.assign({}, this.form);
      }

      this.clearForm();
      alert("Data added successfully!");
    },

    onSubmit() {
      this.visible = true;
      let config = {
        app_name: this.appName,
        programming_langauge: this.programmingLanguage,
      };

      let formData = {
        config: config,
        data: this.data,
      };
      let id = (this.random = Math.floor(Math.random() * 1000000000) + 1);

      let self = this;

      axios
        .post("http://localhost:9000/recommend/", formData)
        .then(function (response) {
          self.visible = false;

          self.zip_path = response.data.zip_link;
          let d_btn = document.getElementById("download_btn");
          d_btn.style.display = "block";
          alert("Project built successfully!");
        })
        .catch((err) => {
          self.visible = false;

          // console.log(err);
          let d_btn = document.getElementById("download_btn");
          d_btn.style.display = "none";
          alert("Oops ! Something Went wrong ");
        });
    },
    downloadProject() {
      window.location.href = require(`../../../../../PastProjects/${this.zip_path}`);
    },
  },
};
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
.image {
  position: absolute;
  top: 68%;
  left: 50%;
  transform: translate(-50%, -50%);
  height: 100px;
}
</style>