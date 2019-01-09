<!doctype html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user" content="{{ logged_user() }}">
    <meta name="git" content="{{ git() }}">
    <link rel="manifest" href="/manifest.json">
    <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' rel="stylesheet">
    <link rel="shortcut icon" type="image/png"
          href="https://twibbon.blob.core.windows.net/twibbon/2014/303/d9b1e1c1-e3ea-4e6f-9daf-0c6e2d10dab0.png">
    <title>@yield('title')</title>
    <style>
        [v-cloak] { display: none }
    </style>
</head>
<body>
<v-app id="app" v-cloak>
    <snackbar></snackbar>
    <v-navigation-drawer
            v-model="drawer"
            fixed
            app
            clipped
    >
        <v-list dense>
            <template v-for="item in items">
                <v-layout
                        v-if="item.heading"
                        :key="item.heading"
                        row
                        align-center
                >
                    <v-flex xs6>
                        <v-subheader v-if="item.heading">
                            @{{ item.heading }}
                        </v-subheader>
                    </v-flex>
                    <v-flex xs6 class="text-xs-center">
                        <a href="#!" class="body-2 black--text">EDIT</a>
                    </v-flex>
                </v-layout>
                <v-list-group
                        v-else-if="item.children"
                        v-model="item.model"
                        :key="item.text"
                        :prepend-icon="item.model ? item.icon : item['icon-alt']"
                        append-icon=""
                >
                    <v-list-tile slot="activator" :href="item.url">
                        <v-list-tile-content>
                            <v-list-tile-title>
                                @{{ item.text }}
                            </v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>
                    <v-list-tile
                            v-for="(child, i) in item.children"
                            :key="i"
                            :href="child.url"
                    >
                        <v-list-tile-action v-if="child.icon">
                            <v-icon>@{{ child.icon }}</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>
                                @{{ child.text }}
                            </v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </v-list-group>
                <v-list-tile v-else :key="item.text" :href="item.url">
                    <v-list-tile-action>
                        <v-icon>@{{ item.icon }}</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>
                        <v-list-tile-title>
                            @{{ item.text }}
                        </v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
            </template>
        </v-list>
    </v-navigation-drawer>
    <v-navigation-drawer
            v-model="drawerRight"
            fixed
            right
            clipped
            app
    >
        <v-card>
            <v-card-title class="blue darken-3 white--text"><h4>Perfil</h4></v-card-title>
            <v-layout row wrap>
                <v-flex xs12>
                    <ul>
                        <li>Nom : {{ Auth::user()->name }}</li>
                        <li>Email : {{ Auth::user()->email }}</li>
                        <li>Admin : {{ Auth::user()->admin }}</li>
                        <li>Roles : {{ implode(',',Auth::user()->map()['roles']) }}</li>
                        <li>Permissions : {{ implode(', ',Auth::user()->map()['permissions']) }}</li>
                    </ul>
                </v-flex>
            </v-layout>
        </v-card>
        <v-card>
            <v-card-title class="blue darken-3 white--text"><h4>Opcions administrador</h4></v-card-title>

            <v-layout row wrap>
                @impersonating
                <v-flex xs12>
                    <v-avatar title="{{ Auth::user()->impersonatedBy()->name }} ( {{ Auth::user()->email }} )">
                        <img src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->impersonatedBy()->email) }}"
                             alt="avatar">
                    </v-avatar>
                </v-flex>
                @endImpersonating
                <v-flex xs12>
                    @canImpersonate
                    <impersonate label="Entrar com..." url="/api/v1/regular_users"></impersonate>
                    @endCanImpersonate
                    @impersonating
                    {{ Auth::user()->impersonatedBy()->name }} està suplantant {{ Auth::user()->name }}
                    <a href="impersonate/leave">Abandonar la suplantació</a>
                    @endImpersonating
                </v-flex>
            </v-layout>
        </v-card>
    </v-navigation-drawer>
    <v-toolbar color="primary" dark fixed app clipped-right clipped-left>
        <v-toolbar-side-icon @click.stop="drawer = !drawer"></v-toolbar-side-icon>
        <v-toolbar-title>Menú</v-toolbar-title>
        <v-spacer></v-spacer>

        <span class="mr-4" v-role="'SuperAdmin'"><git-info></git-info></span>

        <v-avatar @click.stop="drawerRight = !drawerRight" title="{{Auth::user()->name}}({{(Auth::user()->email)}})">
            <img src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}" alt="avatar">
        </v-avatar>
        <v-form action="logout" method="POST">
            @csrf
            <v-btn color="error" type="submit">Logout</v-btn>
        </v-form>
    </v-toolbar>
    <v-content>
        <v-container fluid fill-height>
            <v-layout>
                <v-flex>
                    @yield('content')
                </v-flex>
            </v-layout>
        </v-container>
    </v-content>
    <v-footer color="primary" app>
        <span class="white--text pl-2">Created by Sergi Baucells Rodríguez, &copy; 2018 All rights reserved</span>
    </v-footer>
</v-app>

<script src="{{ mix('/js/app.js') }}"></script>
</body>
</html>