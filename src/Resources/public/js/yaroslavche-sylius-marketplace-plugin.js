document.addEventListener('DOMContentLoaded', function (event) {
    Vue.component('YaroslavcheSyliusMarketplacePluginList', {
        template: '#YaroslavcheSyliusMarketplacePluginList',
        data: function () {
            return {
                plugins: [],
            };
        },
        methods: {
            fetchPlugins: function () {
                axios.post('/admin/plugins/list', {
                    filter: {},
                    sort: {favers: 'desc'}
                })
                    .then(response => {
                        this.plugins = response.data;
                    })
                    .catch(error => console.log(error));
            }
        },
        created: function () {
            this.fetchPlugins();
        }
    });

    const marketplace = new Vue({
        delimiters: ['${', '}'],
        el: '#yaroslavche-sylius-marketplace-plugin'
    });
});
