@extends('layouts.app')

@section('title')
    NewsLetters
@endsection

@section('content')
    <newsletters :newsletter="{{ $newsletter }}"></newsletters>
@endsection
