@extends('layouts.app')

@section('title')
    Tasques
@endsection
@section('content')
    <v-card>
        <v-toolbar color="cyan" dark>

            <v-toolbar-title>Tasques</v-toolbar-title>

        </v-toolbar>

        <v-list>

            <?php foreach ($tasks as $task) : ?>

            <v-list-tile>

                <v-list-tile-avatar>
                    <img src="https://placeimg.com/100/100/any">
                </v-list-tile-avatar>
                @if($task->completed)
                    <del>{{ $task->name }}</del>

                    <form action="/tasks/{{ $task->id }}" method="POST">
                        @csrf
                        {{ method_field('DELETE') }}
                        <v-btn color="error">
                            <button>Eliminar</button>
                        </v-btn>
                    </form>

                @else
                    {{ $task->name }}

                    <form action="" method="POST">
                        @csrf
                        {{ method_field('PUT') }}
                        <input type="hidden" name="id" value="{{ $task->id  }}">
                        <v-btn color="warning">
                            <button>Completar</button>
                        </v-btn>
                    </form>

                    <v-btn color="info" href="/task_edit/{{ $task->id }}">
                        <button>Modificar</button>
                    </v-btn>

                    <form action="/tasks/{{ $task->id }}" method="POST">
                        @csrf
                        {{ method_field('DELETE') }}
                        <v-btn color="error">
                            <button>Eliminar</button>
                        </v-btn>
                    </form>
                @endif
            </v-list-tile>
            <?php endforeach;?>

            <form action="/tasks" method="POST">
                @csrf
                <input name="name" type="text" placeholder="Nova tasca" required>
                <v-btn color="success">
                    <button>Afegir</button>
                </v-btn>
            </form>

        </v-list>

    </v-card>
@endsection
