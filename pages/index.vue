<template>
<v-layout column justify-center align-center>
  <v-flex xs12 sm8 md6>
    <div class="text-xs-center">
    <!--   <img src="/v.png" alt="Vuetify.js" class="mb-5" /> -->
    </div>

    <v-card>
      <v-card-title class="headline">Inserisci importo</v-card-title>
      <v-card-text>
        <div v-if="!isInSell">
        <v-form v-model="valid" >
          <!--   <vselect></vselect> -->
          <v-text-field label="Cliente" v-model="id" required></v-text-field>
          <v-text-field label="Importo" v-model="importo" required></v-text-field>

        </v-form>
          <v-btn  @click="xsubmit()" :disabled="isBtnActive()" >Invia</v-btn>
      </div>
      <div v-else>
        <button  @click="newSell()" >Nuova Vendita</button>
      </div>
      </v-card-text>
    </v-card>
  </v-flex>
<v-flex xs12 sm12 md12>
  <div class="descr-rete"><h2>Importi inseriti :</h2></div>
  <v-data-table :headers="headers" :items="$store.state.sellList" hide-actions class="elevation-1 ">
     <template slot="items" slot-scope="props">
       <td class="text-xs-left">{{ props.item.name }}</td>
       <td class="text-xs-right">{{ props.item.email }}</td>
       <td class="text-xs-right">{{ props.item.price }}</td>
       <td class="text-xs-right">{{ props.item.data_ins }}</td>
     </template>
   </v-data-table>
</v-flex>


</v-layout>
</template>

<script>
var components={};
if (process.browser) {
   components.vselect = require('vue-select')
}

export default {
    components,
  data () {
    return {
      headers: [
         { text: 'Nome', align: 'left',sortable: true, value: 'name'},
         { text: 'Email', align: 'left', value: 'email' },
         { text: 'price', value: 'price' },
         { text: 'data', value: 'data_ins' },
       ],

      valid: false,
      name: '',
      id:null,
      importo: null,
      isInSell:false
    }
},
methods: {
  newSell(){
    this.isInSell=false
    this.name=''
    this.id=null
    this.importo=0
  },
  xsubmit(event){
    const self = this
    if (self.id && self.importo)
      this.$store.dispatch('sell',   {id : self.id, importo : self.importo});

    this.isInSell=true
  },
  isBtnActive(){

     const val = this.id === null || this.id === ''  || this.importo===null ||  this.importo=='' || this.importo === '0' ? false : true
    console.log('activ',val)
    return !val
  }
},
created(){
    console.log('tree comp has crated')
    this.$store.dispatch('getSellList');
}

}
</script>
