@extends('layouts.horizontalLayout')

@section('title', 'Kanban Board')

@section('content')
<div id="kanban-app" data-board-uuid="{{ $boardUuid }}">
    <kanban-board></kanban-board>
</div>
@endsection

@section('page-script')
@if(config('app.env') === 'production')
    <script src="{{ asset('js/components-loader.js') }}"></script>
@else
    @vite(['resources/js/components-loader.js'])
@endif
@endsection
