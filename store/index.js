import Axios from 'axios'



export const state = () => ({
 payments: [],
    balance: {
        spend: 0,
        cashback: 0,
        network: 0,
        saldo: 0
    }
})

export const actions = {
     getPayment(state) {
        const {data} =  Axios.get('http://www.19thstreet.it/naike/a/shop/api.php?api=payment').then(res=>{
            state.commit('setPayments', res.data)
        })

    }
}

export const mutations = {
    setPayments: (state, data) => {
        console.log('payment')
        state.payments = Object.assign({}, data)
    }
}
