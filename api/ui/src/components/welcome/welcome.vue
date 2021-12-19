<template>
  <div id="welcome">
    <h1>Time to Build an API!</h1>
    <p>You found the best place to build - and now you're just  few step away from it!</p>
    <div class="cta">
      <router-link to="/scratch">Start From  Scratch</router-link>
      <router-link to="/recommend">Start From Recommend</router-link>
    </div>
  </div>
</template>

<style scoped>
  #welcome {
    width: 80%;
    margin: auto;
  }

  h1 {
    text-align: center;
  }

  p {
    text-align: center;
  }

  .cta {
    width: 400px;
    margin: auto;
    text-align: center;
  }

  .cta a {
    margin: 10px;
    text-decoration: none;
    display: inline-block;
    border: 1px solid #521751;
    border-radius: 3px;
    width: 150px;
    padding: 10px;
    box-sizing: border-box;
    color: #521751;
    
  }

  .cta a:hover,
  .cta a:active {
    background-color: #521751;
    color: white;
  }
</style>