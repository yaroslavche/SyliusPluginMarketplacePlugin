document.addEventListener('DOMContentLoaded', function (event) {
    Vue.component('YaroslavcheSyliusMarketplacePluginList', {
        template: '#YaroslavcheSyliusMarketplacePluginList',
        data: function () {
            return {
                descriptionLength: 100,
                perPage: 15,
                currentPage: 0,
                currentSort: {field: 'favers', direction: 'asc'},
                plugins: [],
                visible: [],
                pagesCount: 0,
            };
        },
        methods: {
            fetchPlugins: function () {
                axios.post('/admin/plugins/list').then(response => {
                    if (response.data.status === 'success' && response.data.hasOwnProperty('plugins')) {
                        this.plugins = response.data.plugins;
                        this.pagesCount = Math.ceil(this.plugins.length / this.perPage);
                        this.sort();
                    }
                }).catch(error => console.error(error));
            },
            sort: function (field = 'favers') {
                let direction = this.currentSort.direction;
                if (this.currentSort.field === field) {
                    direction = this.currentSort.direction === 'desc' ? 'asc' : 'desc';
                }
                this.currentSort = {field, direction};
                this.plugins = _.sortBy(this.plugins, field);
                if (direction === 'desc') {
                    this.plugins = this.plugins.reverse();
                }
                this.selectVisible();
            },
            selectVisible: function () {
                this.visible = this.plugins.slice(this.currentPage * this.perPage, (this.currentPage + 1) * this.perPage);
            },
            setPage: function (page = 1) {
                this.currentPage = --page;
                this.selectVisible();
            },
            prevPage: function () {
                console.warn(this.currentPage);
                if (this.currentPage < 1) {
                    return;
                }
                this.currentPage--;
                this.selectVisible();
            },
            nextPage: function () {
                if (this.currentPage >= this.pagesCount - 1) {
                    return;
                }
                this.currentPage++;
                this.selectVisible();
            },
        },
        created: function () {
            this.fetchPlugins();
        }
    });

    const marketplace = new Vue({
        el: '#yaroslavche-sylius-marketplace-plugin'
    });
});
