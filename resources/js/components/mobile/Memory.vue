<template>
    <span>
        <v-layout row justify-center>
            <v-dialog v-model="dialog" :fullscreen="$vuetify.breakpoint.smAndDown" hide-overlay transition="dialog-bottom-transition"
                      @keydown.esc.stop.prevent="dialog=false">

              <v-btn slot="activator" @click="memory" color="primary">
                  Memòria del dispositiu
              </v-btn>

                <v-card>

                    <v-toolbar color="primary" class="white--text">
                        <v-btn flat icon class="white--text" @click="dialog=false">
                            <v-icon class="mr-2">close</v-icon>
                        </v-btn>
                        <v-card-title class="headline">Memòria del dispositiu</v-card-title>
                    </v-toolbar>

                    <v-card-text class="text-xs-center">

                        El teu dispositiu té <b id="result">unknown</b> GiB de memòria RAM.

                    </v-card-text>

                    <v-divider></v-divider>

                    <v-card-actions>
                      <v-spacer></v-spacer>
                      <v-btn color="primary" flat @click="dialog = false">
                        Sortir
                      </v-btn>
                    </v-card-actions>
              </v-card>
            </v-dialog>
        </v-layout>

    </span>
</template>

<script>
export default {
  name: 'Memory',
  data () {
    return {
      dialog: false
    }
  },
  methods: {
    memory () {
      document.getElementById('result').innerHTML = navigator.deviceMemory || 'unknown'
    }
  }
}
</script>
