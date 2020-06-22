<template>
  <li class="nav-item dropdown" v-if="notifications.length">
    <a class="nav-link " href="#" role="button" data-toggle="dropdown">
        
    <span class="fa fa-bell"></span>
        
    </a>

    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

      <a
         class="dropdown-item" 
        :href="notification.data.link" 
        v-for="notification in notifications" 
        v-text="notification.data.message"
        @click="markAsRead(notification)"
        >
        
      </a>
   
    </div>
  </li>
</template>

<script>
export default {
    data() {
        return {notifications: false}
    },

    created() {
        axios.get("/profiles/" + window.App.user.name + "/notifications")
        .then(response =>this.notifications = response.data);
    },

    methods: {
        markAsRead(notification) {
            axios.delete('/profiles/' + window.App.user.name + '/notifications/' + notification.id)
        }
    }
};
</script>