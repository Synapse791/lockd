let App = Vue.extend({});

let router = new VueRouter();

router.map({
    '/passwords': {
        component: Passwords
    },
    '/search': {
        component: Search
    }
});

router.start(App, '#dashboardPage');