<template>
<v-layout column justify-center align-center>
  <v-flex xs12 sm8 md6>
    <div class="text-xs-center">
    <!--   <img src="/v.png" alt="Vuetify.js" class="mb-5" /> -->
    </div>

    <v-card>
      <v-card-title class="headline">Inserisci importo</v-card-title>
      <v-card-text>
        <v-form v-model="valid">
          <v-text-field label="Cliente" v-model="name" required></v-text-field>
          <v-text-field label="Importo" v-model="importo" required></v-text-field>
        </v-form>

      </v-card-text>
    </v-card>

    <!--  <v-card>
        <v-card-title class="headline">Welcome to the Vuetify + Nuxt.js template</v-card-title>
        <v-card-text>
          <p>Vuetify ss is a progressive Material Design component framework for Vue.js. It was designed to empower developers to create amazing applications.</p>
          <p>For more information on Vuetify, check out the <a href="https://vuetifyjs.com" target="_blank">documentation</a>.</p>
          <p>If you have questions, please join the official <a href="https://chat.vuetifyjs.com/" target="_blank" title="chat">discord</a>.</p>
          <p>Find a bug? Report it on the github <a href="https://github.com/vuetifyjs/vuetify/issues" target="_blank" title="contribute">issue board</a>.</p>
          <p>Thank you for developing with Vuetify and I look forward to bringing more exciting features in the future.</p>
          <div class="text-xs-right">
            <em><small>&mdash; John Leider</small></em>
          </div>
          <hr class="my-3">
          <a href="https://nuxtjs.org/" target="_blank">Nuxt Documentation</a>
          <br>
          <a href="https://github.com/nuxt/nuxt.js" target="_blank">Nuxt GitHub</a>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" flat nuxt to="/inspire">Continue</v-btn>
        </v-card-actions>
      </v-card> -->
  </v-flex>
</v-layout>
</template>

<script>
export default {
  data () {
    return {
      valid: false,
      name: 'aaa',
      importo: 0
    }
},
created(){
    console.log('tree comp has crated')
}

}
</script>
