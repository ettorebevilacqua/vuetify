<template>
<v-layout>
    <v-flex text-xs-center>
        <h2>Rete :</h2>

    <div v-for="(item, index) in $store.state.payments">
        <span>nome :{{item.firstname}} {{item.lastname}} user {{item.email}} price :{{item.tot}}</span>
    </div>
        <!-- <tree-view style="font-size:12px" :nodes="rete.nodes" :depth="0" :label="rete.label"></tree-view> -->
        </div>

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


<script>
//  import  "tree-vue-component"
import TreeView from '~/components/tree'

const treeData = {
    text: 'root',
    nodes: [{
            text: 'item1',
            nodes: [{
                    text: 'item1.1'
                },
                {
                    text: 'item1.2',
                    nodes: [{
                        label: 'item1.2.1'
                    }]
                }
            ]
        },
        {
            text: 'item2'
        }
    ]
}

const data = {
    label: 'la mia struttura ',
    nodes: [{
            label: 'rossi luigi',
            tot: 5000,
            nodes: [{
                    label: 'qui',
                    tot: 2000,
                    nodes: [{
                        label: 'topolino',
                        tot: 2000,
                        nodes: [

                        ]
                    }]
                },
                {
                    label: 'quo',
                    tot: 2000,
                    nodes: [{
                            label: 'bassotto1',
                            tot: 2000,
                            nodes: [

                            ]
                        },
                        {
                            label: 'bassotto2',
                            tot: 2000,
                            nodes: [

                            ]
                        }
                    ]
                },
                {
                    label: 'qua',
                    tot: 2000,
                    nodes: [{
                            label: 'gastone',
                            tot: 2000,
                            nodes: [

                            ]
                        },
                        {
                            label: 'paperone',
                            tot: 2000,
                            nodes: [{
                                    label: 'papera',
                                    tot: 2000,
                                    nodes: [

                                    ]
                                },
                                {
                                    label: 'paperino',
                                    tot: 2000,
                                    nodes: [

                                    ]
                                }
                            ]
                        }
                    ]
                }
            ]
        },
        {
            label: 'bianchi fra',
            tot: 2000,
            nodes: [

            ]
        },
        {
            label: 'verdi etto',
            tot: 1000,
            nodes: [

            ]
        },
        {
            label: 'grigi manu',
            tot: 7000,
            nodes: [

            ]
        }
    ]
}

export default {
    components: {
        TreeView
    },
    data() {
        return {
            rete: data,
            treeData: treeData,
        }
    },

    methods: {
        // const tree=[{text:"node 1",value:{id:1},state:{opened:!0},children:[{text:"node 11",value:{id:11}},{text:"node 12",value:{id:12},state:{opened:!0},children:[{text:"node 121",value:{id:121}},{text:"node 122",value:{id:122}},{text:"node 123",value:{id

        getData() {
            return this.data.reduce((acc, val) => {

            }, [])
        }
    },
    created (){
        console.log('inspare');
        this.$store.dispatch('getPayment');
    }
}
</script>
