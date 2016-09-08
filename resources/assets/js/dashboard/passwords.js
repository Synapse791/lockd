let Passwords = Vue.extend({
    template: `<section class="password screen">
                    <article class="ui message">
                        <div class="ui big breadcrumb">
                            <a class="section">Root</a>
                            <span v-for="crumb in breadcrumb">
                                <i class="right chevron icon divider"></i>
                                <a class="section" v-on:click="loadContents(crumb.id)">{{ crumb.name }}</a>
                            </span>
                        </div>
                    </article>
                    <div class="ui grid">
                        <div class="three wide computer eight wide tablet column" v-for="folder in folders">
                            <div class="ui fluid link card">
                                <div class="image">
                                    <img src="/images/folder.png" alt="Folder">
                                </div>
                                <div class="center aligned content">
                                    <div class="header">{{ item.name }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="three wide computer eight wide tablet column" v-for="password in passwords">
                            <div class="ui fluid card">
                                <div class="content">
                                    <div class="header">{{ item.name }}</div>
                                    <hr>
                                    <table class="ui table">
                                        <tbody>
                                        <tr>
                                            <td><strong>URL</strong></td>
                                            <td><a href="{{ item.url || '' }}">{{ item.url || 'none' }}</a></td>
                                        </tr>
                                        <tr>
                                            <td><strong>User</strong></td>
                                            <td>{{ item.user || 'none' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Password</strong></td>
                                            <td><span id="password_{{ item.id }}">&ast;&ast;&ast;&ast;&ast;&ast;</span></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="ui two bottom attached buttons">
                                    <button class="ui blue labeled icon button" v-on:click="showPassword(item.id)">
                                        <i class="search icon"></i>
                                        Show Password
                                    </button>
                                    <button class="ui primary labeled icon button" v-on:click="copyPassword(item.id)">
                                        <i class="copy icon"></i>
                                        Copy Password
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="password_copy_container">
                        <button id="copy_trigger" data-clipboard-text="{{ hiddenPassword }}"></button>
                    </div>
    `,

    data() {
        return {
            hiddenPassword: '',
            folders: [],
            passwords: []
        }
    },

    ready() {
        setTimeout(() => {
            new Clipboard('#copy_trigger');
            this.loadContents(1);
        }, 1000);
    },

    methods: {

        loadContents(folderId) {
            this.loadFolders(folderId);
            this.loadPasswords(folderId);
        },

        loadFolders(folderId) {
            $q.get(`/api/folder/${folderId}/folders`)
                .success(({data}) => {
                    this.folders = data;
                });
        },

        loadPasswords(folderId) {
            $q.get(`/api/folder/${folderId}/passwords`)
                .success(({data}) => {
                    this.passwords = data;
                });
        },

        showPassword(id) {
            let el = $(`#password_${id}`);
            let password = '';
            this.items.forEach((item) => {
                if (item.type == 'password' && item.id == id)
                    password = item.password;
            });

            el.html(atob(password));

            setTimeout(() => {el.html('&ast;&ast;&ast;&ast;&ast;&ast;')}, 3000);
        },

        copyPassword(id) {
            let password = '';
            this.items.forEach((item) => {
                if (item.type == 'password' && item.id == id)
                    password = item.password;
            });

            this.hiddenPassword = atob(password);
            setTimeout(() => {$('#copy_trigger').click()}, 50);
            toastr.info('Copied password to clipboard');
            setTimeout(() => {this.hiddenPassword = ''}, 100);

        }
    }
});