@extends('layouts.horizontalLayout')

@section('title', 'Meus Quadros')

@section('content')
<div id="tarefas-app">
    <tarefas-index></tarefas-index>
</div>
@endsection

@section('page-script')
@if(config('app.env') === 'production')
    <script src="{{ asset('js/components-loader.js') }}"></script>
@else
    @vite(['resources/js/components-loader.js'])
@endif
@endsection
