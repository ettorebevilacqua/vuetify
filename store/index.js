import Axios from 'axios'

// Axios.defaults.headers.common['Access-Control-Allow-Credentials'] = 'true'
// Axios.defaults.headers.common['withCredentials'] = 'true'

const webApiPath = 'http://www.ekores.org/htm/ekores/shop/api.php?api='

const myApi = Axios.create({
//  baseURL: 'http://someUrl/someEndpoint',
  timeout: 10000,
  withCredentials: true,
  // transformRequest: [(data) => JSON.stringify(data.data)],
  headers: {
    'Accept': 'application/json'
  //  'Content-Type': 'application/json'
    /*    'Access-Control-Allow-Credentials': 'true',
    // 'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Headers': 'Origin, X-Requested-With',
    'credentials': 'same-origin',
    'withCredentials': 'true', */
    // 'Cache': 'no-cache'
  }
})

myApi.get(webApiPath + 'userInfo').then(res => console.log(res.data))

export const state = () => ({
  payments: [],
  balance: {'prov': {'amount': '0', 'prov': '0'}, 'cashBack': {'amount': '0', 'cashback': '0'}, 'balance': 0},
  user: {},
  sell: {},
  sellList: []
})

export const actions = {
  sell (state, payload) {
    return myApi.post(webApiPath + 'sell', payload).then(res => {
      state.commit('setSell', res.data)
      state.dispatch('getSellList')
    })
  },
  getPayment (state) {
    return myApi.get(webApiPath + 'payment').then(res => {
      state.commit('setPayments', res.data)
    })
  },
  getBalance (state) {
    return myApi.get(webApiPath + 'balance').then(res => {
      state.commit('setBalance', res.data)
    })
  },

  getUser (state) {
    return myApi.get(webApiPath + 'userInfo', {withCredentials: true}).then(res => {
      state.commit('setUserInfo', res.data)
    })
  },

  getSellList (state) {
    return myApi.get(webApiPath + 'seelList', {withCredentials: true}).then(res => {
      state.commit('setSeelList', res.data)
    })
  }

}

export const mutations = {
  setPayments: (state, data) => {
    state.payments = data // Object.assign({}, data)
  },
  setSeelList: (state, data) => {
    state.sellList = data // Object.assign({}, data)
  },
  setBalance: (state, data) => {
    state.balance = data
  },
  setUserInfo: (state, data) => {
    state.user = data.user
    state.balance = data.balance
  },
  setSell: (state, data) => {
    state.sell = data
  }

}
