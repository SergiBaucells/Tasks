<template>
    <span>

        <v-dialog v-model="createDialog" fullscreen transition="dialog-bottom-transition"
                  @keydown.esc="createDialog=false">
            <v-toolbar color="primary" class="white--text">
                <v-btn flat icon class="white--text" @click="createDialog=false">
                    <v-icon class="mr-2">close</v-icon>
                </v-btn>
                <v-card-title class="headline">Crear tag</v-card-title>
                <v-spacer></v-spacer>
                <v-btn flat class="white--text" @click="createDialog=false">
                    <v-icon class="mr-2">exit_to_app</v-icon>
                    Sortir
                </v-btn>
                <v-btn flat class="white--text" @click="create()">
                    <v-icon class="mr-2">save</v-icon>
                    Guardar
                </v-btn>
            </v-toolbar>
            <v-card>
                <v-card-text>
                    <v-form>
                        <v-text-field v-model="newTag.name" label="Nom" hint="Nom del tag"
                                      placeholder="Nom del tag"></v-text-field>
                        <input type="color" v-model="newTag.color" style="width: 30px; height: 30px;">
                        <v-textarea v-model="newTag.description" label="Descripció" item-value="id"></v-textarea>
                        <div class="text-xs-center">
                            <v-btn @click="createDialog=false">
                                <v-icon class="mr-2">exit_to_app</v-icon>
                                Cancel·lar
                            </v-btn>
                            <v-btn color="success"
                                   @click="create()"
                                   :loading="creating"
                                   :disabled="creating">
                                <v-icon class="mr-2">save</v-icon>
                                Guardar
                            </v-btn>
                        </div>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>

        <v-dialog v-model="editDialog" fullscreen transition="dialog-bottom-transition"
                  @keydown.esc="editDialog=false">
            <v-toolbar color="primary" class="white--text">
                <v-btn flat icon class="white--text" @click="editDialog=false">
                    <v-icon class="mr-2">close</v-icon>
                </v-btn>
                <v-toolbar-title class="white--text">Editar Tag</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn flat class="white--text" @click="editDialog=false">
                    <v-icon class="mr-2">exit_to_app</v-icon>
                    Sortir
                </v-btn>
                <v-btn flat class="white--text" @click="edit">
                    <v-icon class="mr-2">save</v-icon>
                    Guardar
                </v-btn>
            </v-toolbar>
            <v-card>
                <v-card-text>
                    <v-form>
                        <v-text-field v-model="tagBeingUpdated.name" label="Nom" hint="Nom de la tag"
                                      placeholder="Nom de la tag"></v-text-field>
                        <input type="color" v-model="tagBeingUpdated.color" style="width: 30px; height: 30px;">
                        <v-textarea v-model="tagBeingUpdated.description" label="Descripció"></v-textarea>
                        <div class="text-xs-center">
                            <v-btn @click="editDialog=false">
                                <v-icon class="mr-2">exit_to_app</v-icon>
                                Cancel·lar
                            </v-btn>
                            <v-btn color="success" @click="edit"
                            >
                                <v-icon class="mr-2">save</v-icon>
                                Guardar
                            </v-btn>
                        </div>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>

        <v-dialog v-model="showDialog" fullscreen transition="dialog-bottom-transition"
                  @keydown.esc="showDialog=false">
            <v-toolbar color="primary" class="white--text">
                <v-btn icon flat class="white--text" @click="showDialog=false">
                    <v-icon class="mr-2">close</v-icon>
                </v-btn>
                <v-toolbar-title class="white--text">Mostrar tag</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn flat class="white--text" @click="showDialog=false">
                    <v-icon class="mr-2">exit_to_app</v-icon>
                    Sortir
                </v-btn>
            </v-toolbar>
            <v-card>
                <v-card-text>
                    <v-form>
                        <v-text-field v-model="tagBeingShow.name" label="Nom" hint="Nom de la tag"
                                      placeholder="Nom de la tag" readonly></v-text-field>
                        <input disabled type="color" v-model="tagBeingShow.color" style="width: 30px; height: 30px;">
                        <v-textarea v-model="tagBeingShow.description" label="Descripció" readonly></v-textarea>
                        <div class="text-xs-center">
                            <v-btn @click="showDialog=false">
                                <v-icon class="mr-2">exit_to_app</v-icon>
                                Sortir
                            </v-btn>
                        </div>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>

        <v-toolbar color="primary">
            <v-menu>
                <v-btn slot="activator" icon dark>
                    <v-icon>more_vert</v-icon>
                </v-btn>
                <v-list>
                    <v-list-tile href="http://google.com">
                        <v-list-tile-title>Google</v-list-tile-title>
                    </v-list-tile>
                </v-list>
            </v-menu>
            <v-toolbar-title class="white--text">Tags</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon class="white--text">
                <v-icon>settings</v-icon>
            </v-btn>
            <v-btn icon class="white--text" @click="refresh" :loading="loading" :disabled="loading">
                <v-icon>refresh</v-icon>
            </v-btn>
        </v-toolbar>
        <v-card>
            <v-card-title>
                <v-layout wrap>
                    <v-flex lg3 class="pr-2">
                        <v-text-field
                                append-icon="search"
                                label="Buscar"
                                v-model="search"
                        ></v-text-field>
                    </v-flex>
                </v-layout>
            </v-card-title>
            <v-data-table
                    :headers="headers"
                    :items="dataTags"
                    :search="search"
                    no-results-text="No s'ha trobat cap registre coincident"
                    no-data-text="No hi ha dades disponibles"
                    rows-per-page-text="Tags per pàgina"
                    :rows-per-page-items="[5,10,25,50,100,200,{'text':'Tots','value':-1}]"
                    :loading="loading"
                    :pagination.sync="pagination"
                    class="hidden-md-and-down"
            >
                <v-progress-linear slot="progress" color="secondary" indeterminate></v-progress-linear>
                <template slot="items" slot-scope="{item: tag}">
                    <tr>
                        <td>{{ tag.id }}</td>
                        <td v-text="tag.name" :style="'color:' + tag.color"></td>
                        <td class="text-xs-left">
                            <div :style="'background-color:' + tag.color + ';border-radius: 7px; height: 10px; width: 30px;'"></div>
                        </td>
                        <td v-text="tag.description"></td>
                        <td>
                            <span :title="tag.created_at_formatted">{{ tag.created_at_human }}</span>
                        </td>
                        <td>
                            <span :title="tag.updated_at_formatted">{{ tag.updated_at_human }}</span>
                        </td>
                        <td>
                            <v-btn v-if="$can('tags.show', tags)" icon color="secondary" flat
                                   title="Mostrar la tag"
                                   @click="showTag(tag)">
                                <v-icon>visibility</v-icon>
                            </v-btn>
                            <v-btn v-if="$can('tags.update', tags)" icon color="success" flat
                                   title="Editar la tag"
                                   @click="showUpdate(tag)">
                                <v-icon>edit</v-icon>
                            </v-btn>
                            <v-btn v-if="$can('tags.destroy', tags)" icon color="error" flat
                                   title="Eliminar la tag"
                                   :loading="removing === tag.id" :disabled="removing === tag.id" @click="destroy(tag)">
                                <v-icon>delete</v-icon>
                            </v-btn>
                        </td>
                    </tr>
                </template>
            </v-data-table>
            <v-data-iterator class="hidden-lg-and-up"
                             :items="dataTags"
                             :search="search"
                             no-results-text="No s'ha trobat cap registre coincident"
                             no-data-text="No hi ha dades disponibles"
                             rows-per-page-text="Tags per pàgina"
                             :rows-per-page-items="[5,10,25,50,100,200,{'text':'Tots','value':-1}]"
                             :loading="loading"
                             :pagination.sync="pagination"
            >
                <v-flex
                        slot="item"
                        slot-scope="{item:tag}"
                        xs12
                        sm6
                        md4
                >
                    <v-card class="mb-1">
                        <v-card-title v-text="tag.name"></v-card-title>
                        <v-list dense>
                            <v-list-tile>
                              <v-list-tile-content>User:</v-list-tile-content>
                              <v-list-tile-content class="align-end">{{ tag.user_id }}</v-list-tile-content>
                            </v-list-tile>
                        </v-list>
                    </v-card>
                </v-flex>
            </v-data-iterator>
        </v-card>
        <v-btn
                v-if="$can('tags.store', tags)"
                @click="showCreate"
                fab
                bottom
                right
                fixed
                color="secondary"
                class="white--text"
        >
            <v-icon>add</v-icon>
        </v-btn>
    </span>
</template>

<script>
export default {
  name: 'Tags',
  data () {
    return {
      tagBeingUpdated: '',
      tagBeingShow: '',
      newTag: {
        name: '',
        color: '',
        description: ''
      },
      color: '',
      name: '',
      description: '',
      createDialog: false,
      editDialog: false,
      showDialog: false,
      tagBeingRemoved: null,
      search: '',
      pagination: {
        rowsPerPage: 25
      },
      loading: false,
      creating: false,
      editing: false,
      removing: null,
      dataTags: this.tags,
      headers: [
        { text: 'Id', value: 'id' },
        { text: 'Name', value: 'name' },
        { text: 'Color', value: 'color' },
        { text: 'Description', value: 'description' },
        { text: 'Create', value: 'created_at_timestamp' },
        { text: 'Modify', value: 'updated_at_timestamp' },
        { text: 'Actions', sortable: false, value: 'full_search' }
      ]
    }
  },
  props: {
    tags: {
      type: Array,
      required: true
    },
    uri: {
      type: String,
      required: true
    }
  },
  methods: {
    showUpdate (tag) {
      this.editDialog = true
      this.tagBeingUpdated = tag
    },
    showTag (tag) {
      this.showDialog = true
      this.tagBeingShow = tag
    },
    showCreate () {
      this.createDialog = true
    },
    removeTag (tag) {
      this.dataTags.splice(this.dataTags.indexOf(tag), 1)
    },
    createTag (tag) {
      this.dataTags.splice(0, 0, tag)
    },
    editTag (editedTag) {
      this.dataTags.splice(this.dataTags.indexOf(editedTag), 1, editedTag)
    },
    create () {
      window.axios.post(this.uri, this.newTag).then((response) => {
        this.createTag(response.data)
        this.$snackbar.showMessage("S'ha creat correctament")
        this.createDialog = false
        this.newTag.name = ''
        this.newTag.description = ''
        this.newTag.color = ''
        this.refresh()
      }).catch(error => {
        this.$snackbar.showError(error)
      })
    },
    async destroy (tag) {
      let result = await this.$confirm('Les etiquetes esborrades no es poden recuperar!',
        {
          title: 'Esteu segurs?',
          buttonTrueText: 'Eliminar',
          buttonFalseText: 'Cancel·lar',
          color: 'error'
        })
      if (result) {
        this.removing = true
        window.axios.delete(this.uri + '/' + tag.id).then(() => {
          this.removeTag(tag)
          this.tag = null
          this.$snackbar.showMessage("S'ha esborrat correctament")
          this.removing = false
        }).catch(error => {
          this.removing = false
        })
      }
    },
    edit () {
      window.axios.put(this.uri + '/' + this.tagBeingUpdated.id, this.tagBeingUpdated).then((response) => {
        this.editTag(response.data)
        this.$snackbar.showMessage("S'ha actualitzat correctament")
        this.editDialog = false
        this.refresh()
      }).catch(error => {
        this.$snackbar.showError(error)
        this.refresh()
      })
    },
    refresh () {
      this.loading = true
      window.axios.get(this.uri).then(response => {
        this.dataTags = response.data
        this.loading = false
        this.$snackbar.showMessage("S'ha refrescat correctament")
      }).catch(error => {
        console.log(error)
        this.loading = false
      })
    }
  }
}
</script>
