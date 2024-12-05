<template>
    <div class="sidebar">
      <ul>
        <li><router-link to="/">Home</router-link></li>
        <li><router-link to="/about">About</router-link></li>
        <li><router-link to="/contact">Contact</router-link></li>
      </ul>
    </div>
  </template>

  <script>
  export default {
    name: 'Sidebar',
  };
  </script>

  <style>
  .sidebar {
    position: fixed;
    width: 200px;
    height: 100%;
    background-color: #333;
    color: white;
    padding-top: 20px;
  }

  .sidebar ul {
    list-style: none;
    padding: 0;
  }

  .sidebar li {
    padding: 10px;
  }

  .sidebar a {
    color: white;
    text-decoration: none;
  }

  .sidebar a:hover {
    background-color: #575757;
  }
  </style>
