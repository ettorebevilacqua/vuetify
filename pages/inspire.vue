<template>
<v-layout justify-center>
    <v-flex text-xs-center>


<div class="table-with" justify-center>

        <div>
            <div>
                <div class="balance-descr"><h2>CashBack</h2></div>
                <div class="balance-val-container">
                <div class="balance-val txtright">Tot Speso &nbsp;:&nbsp;</div><div class="balance-val txtleft">{{$store.state.balance.cashBack.amount}}</div>
                <div class="balance-val txtright">Provvigione 10% &nbsp;:&nbsp;</div><div class="balance-val txtleft">{{$store.state.balance.cashBack.cashback}}</div>
                </div>
            </div>

            <div>
                <div class="balance-descr"><h2>Rete</h2></div>
                <div class="balance-val-container">
                <div class="balance-val txtright">Tot Speso</div><div class="balance-val txtleft">{{$store.state.balance.prov.amount}}</div>
                <div class="balance-val txtright">Provvigione 10% &nbsp;:&nbsp;</div><div class="balance-val txtleft">{{$store.state.balance.prov.prov}}</div>
            </div>
            </div>
        </div>
        <div class="descr-rete"><h2>Rete :</h2></div>
        <v-data-table :headers="headers" :items="$store.state.payments" hide-actions class="elevation-1 ">
           <template slot="items" slot-scope="props">
             <td class="text-xs-left">{{ props.item.name }}</td>
             <td class="text-xs-right">{{ props.item.mail }}</td>
             <td class="text-xs-right">{{ props.item.tot }}</td>
           </template>
       </v-data-table>
</div>
        <!-- <tree-view style="font-size:12px" :nodes="rete.nodes" :depth="0" :label="rete.label"></tree-view> -->

        <!--  <tree :data="rete"> </tree>     @toggle="toggle($event)" @change="change($event)">
<vue-tree></vue-tree>
-->
        <!--  <img src="/v.png" alt="Vuetify.js" class="mb-5">
    <blockquote class="blockquote">
      &#8220;First, solve the problem. Then, write the code.&#8221;
      <footer>
        <small>
            <em>&mdash;John Johnson</em>
          </small>
      </footer>
    </blockquote> -->

    </v-flex>
</v-layout>
</template>
<style scoped>
    .table-with{
        width: 800px;
    },
    .descr-rete{
        margin-bottom: 16px;
    },
    .left{
        float:left;
    }
    .balance-descr{
        width: 100%;
        clear: left;
        background-color: #303030;
    }
    .txtleft{
        text-align: left;
    }
    .txtright{
        text-align: right;

    }
    .balance-val-container{
            display:flex;
            align-items:center;
            justify-content: center;
    }
    .balance-val{
        float:left;
        xwidth: 120px;
        padding: 5px;
    }
</style>

<script>
//  import  "tree-vue-component"
import TreeView from '~/components/tree'

export default {
    components: {
        TreeView
    },
    data() {
        return {
            headers: [
               { text: 'Nome', align: 'left',sortable: true, value: 'name'},
               { text: 'Email', align: 'left', value: 'email' },
               { text: 'Tot', value: 'tot' }
             ],
        }
    },

    methods: {
        // const tree=[{text:"node 1",value:{id:1},state:{opened:!0},children:[{text:"node 11",value:{id:11}},{text:"node 12",value:{id:12},state:{opened:!0},children:[{text:"node 121",value:{id:121}},{text:"node 122",value:{id:122}},{text:"node 123",value:{id

        getData() {
            return this.data.reduce((acc, val) => {

            }, [])
        }
    },
    created() {
        this.$store.dispatch('getBalance');
        this.$store.dispatch('getPayment');

    }
}
</script>
