<template>
    <v-menu offset-y>
        <v-badge slot="activator" left overlap color="error" class="ml-2 mr-2 mt-2">
            <span slot="badge" v-text="amount"></span>
            <v-btn icon color="white" :loading="loading" :disabled="loading">
                <v-icon :large="large" color="primary">notifications</v-icon>
            </v-btn>
        </v-badge>
        <v-list>
            <v-list-tile v-if="dataNotifications.length > 0">
                <v-list-tile-title>
                    <span v-if="dataNotifications.length === 1">
                        Teniu {{ dataNotifications.length }} notificació pendent:
                    </span>
                    <span v-else>
                        Teniu {{ dataNotifications.length }} notificacions pendents:
                    </span>
                </v-list-tile-title>
            </v-list-tile>
            <v-divider v-if="dataNotifications.length > 0"></v-divider>
            <v-list-tile v-if="dataNotifications.length > 0"
                         v-for="(notification, index) in dataNotifications"
                         :key="index"
                         @click="markAsReaded(notification)"
                         :href="notification.data.url"
                         target="_blank"
            >
                <v-list-tile-content>
                    <v-list-tile-title style="max-width: 450px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <v-icon v-if="notification.data.icon" :color="notification.data.iconColor">{{ notification.data.icon }}</v-icon>
                        <v-tooltip bottom>
                            <span slot="activator">{{ notification.data.title }}</span>
                            <span>{{ notification.data.title }}</span>
                        </v-tooltip>
                    </v-list-tile-title>
                </v-list-tile-content>
            </v-list-tile>
            <v-list-tile v-if="dataNotifications.length === 0">
                <v-list-tile-title>No hi ha cap notificació pendent de llegir</v-list-tile-title>
            </v-list-tile>
            <v-divider></v-divider>
            <v-list-tile>
                <v-list-tile-title class="caption">
                    <a href="/notifications">Veure totes</a> | <span v-if="dataNotifications.length > 0"> <a href="#" @click="markAllAsReaded">Marcar totes com a llegides</a> | </span><a href="#" @click="refresh(true)">Actualitzar</a>
                </v-list-tile-title>
            </v-list-tile>
        </v-list>
    </v-menu>

</template>

<script>
export default {
  name: 'NotificationsWidget',
  data () {
    return {
      dataNotifications: [],
      loading: false
    }
  },
  props: {
    notifications: {
      type: Array,
      required: false
    }
  },
  computed: {
    large () {
      if (this.dataNotifications) return this.dataNotifications.length > 0
      return false
    },
    amount () {
      if (this.dataNotifications) return this.dataNotifications.length
      return 0
    }
  },
  methods: {
    refresh (message = false) {
      this.loading = true
      window.axios.get('/api/v1/user/unread_notifications/').then((response) => {
        this.dataNotifications = response.data
        this.loading = false
        if (message) this.$snackbar.showMessage('Notificacions actualitzades correctament')
      }).catch(() => {
        this.loading = false
      })
    },
    markAsReaded (notification) {
      this.loading = true
      window.axios.delete('/api/v1/user/unread_notifications/' + notification.id).then(() => {
        this.loading = false
        this.refresh()
      }).catch(() => {
        this.loading = false
      })
    },
    markAllAsReaded () {
      this.loading = true
      this.loading = true
      window.axios.delete('/api/v1/user/unread_notifications/all').then(() => {
        this.loading = false
        this.refresh()
      }).catch(() => {
        this.loading = false
      })
    },
    listen () {
      window.Echo.private('App.User.' + window.laravel_user.id)
        .notification((notification) => {
          this.refresh(false)
        })
    }
  },
  created () {
    if (this.notifications) {
      this.dataNotifications = this.notifications
    } else {
      this.loading = true
      window.axios.get('/api/v1/user/unread_notifications').then((response) => {
        this.dataNotifications = response.data
        this.loading = false
      }).catch(() => {
        this.loading = false
      })
    }
    this.listen()
  }
}
</script>
