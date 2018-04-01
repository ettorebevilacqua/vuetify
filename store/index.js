import Axios from 'axios'

export const state = () => ({
  payments: [],
  balance: {'prov': {'amount': '0', 'prov': '0'}, 'cashBack': {'amount': '0', 'cashback': '0'}, 'balance': 0}
})

export const actions = {
  getPayment (state) {
    return Axios.get('http://www.19thstreet.it/naike/a/shop/api.php?api=payment').then(res => {
      state.commit('setPayments', res.data)
    })
  },
  getBalance (state) {
    return Axios.get('http://www.19thstreet.it/naike/a/shop/api.php?api=balance').then(res => {
      state.commit('setBalance', res.data)
    })
  },

  getUser (state) {
    return Axios.get('http://www.19thstreet.it/naike/a/shop/api.php?api=balance').then(res => {
      state.commit('setBalance', res.data)
    })
  }

}

export const mutations = {
  setPayments: (state, data) => {
    state.payments = data // Object.assign({}, data)
  },
  setBalance: (state, data) => {
    state.balance = data
  }

}
