<template>
<v-layout>
  <v-flex text-xs-center>
  <!--  <img src="/v.png" alt="Vuetify.js" class="mb-5">
    <blockquote class="blockquote">
      &#8220;First, solve the problem. Then, write the code.&#8221;
      <footer>
        <small>
            <em>&mdash;John Johnson</em>
          </small>
      </footer>
    </blockquote> -->

  {{rete}}
  </v-flex>
</v-layout>
</template>


<script>
const data = [{
    name: 'rossi luigi',
    tot: 5000,
    childs: [{
        name: 'qui',
        tot: 2000,
        childs: [{
          name: 'topolino',
          tot: 2000,
          childs: [

          ]
        }]
      },
      {
        name: 'quo',
        tot: 2000,
        childs: [{
            name: 'bassotto1',
            tot: 2000,
            childs: [

            ]
          },
          {
            name: 'bassotto2',
            tot: 2000,
            childs: [

            ]
          }
        ]
      },
      {
        name: 'qua',
        tot: 2000,
        childs: [{
            name: 'gastone',
            tot: 2000,
            childs: [

            ]
          },
          {
            name: 'paperone',
            tot: 2000,
            childs: [{
                name: 'papera',
                tot: 2000,
                childs: [

                ]
              },
              {
                name: 'paperino',
                tot: 2000,
                childs: [

                ]}
            ]}
        ]
      }
    ]
  },
  {
    name: 'bianchi fra',
    tot: 2000,
    childs: [

    ]
  },
  {
    name: 'verdi etto',
    tot: 1000,
    childs: [

    ]
  },
  {
    name: 'grigi manu',
    tot: 7000,
    childs: [

    ]
  }
]
export default {
  data() {
    return {
      rete:data
    }
  }
}
</script>
