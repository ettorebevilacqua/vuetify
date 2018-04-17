
export default function ({ store, redirect, route }) {
    if (!store.state.user || !store.state.user.isshop && route.name!=='inspire') {
         return redirect('/inspire')
    //alert('aa');

    }
}
