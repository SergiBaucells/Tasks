<template>
    <span>
        <v-dialog v-model="dialog" :fullscreen="$vuetify.breakpoint.smAndDown" transition="dialog-bottom-transition"
                          @keydown.esc="dialog=false">
            <v-toolbar color="primary" class="white--text">
                <v-btn flat icon class="white--text" @click="dialog=false">
                    <v-icon class="mr-2">close</v-icon>
                </v-btn>
                <v-card-title class="headline">Editar tasca</v-card-title>
                <v-spacer></v-spacer>
                <v-btn flat class="white--text" @click="dialog=false">
                    <v-icon class="mr-2">exit_to_app</v-icon>
                    Sortir
                </v-btn>
                <v-btn flat class="white--text">
                    <v-icon class="mr-2">save</v-icon>
                    Guardar
                </v-btn>
            </v-toolbar>
            <v-card>
                <v-card-text>
                    <task-update-form :task="task" :uri="uri" :users="users" @close="dialog=false" @updated="updated"></task-update-form>
                </v-card-text>
            </v-card>
        </v-dialog>

        <v-btn v-if="$can('user.tasks.update', task)" icon
               color="success" flat
               title="Actualitzar la tasca" @click="dialog=true">
            <v-icon>edit</v-icon>
        </v-btn>
    </span>
</template>

<script>
import TaskUpdateForm from './TaskUpdateForm'
export default {
  'name': 'TaskUpdate',
  components: {
    'task-update-form': TaskUpdateForm
  },
  data () {
    return {
      dialog: false
    }
  },
  props: {
    task: {
      type: Object,
      required: true
    },
    uri: {
      type: String,
      required: true
    },
    users: {
      type: Array,
      required: true
    }
  },
  methods: {
    updated (task) {
      this.$emit('updated', task)
    }
  }
}
</script>
