@extends('layouts.horizontalLayout')

@section('content')
  <div id="app" data-board-id="{{ $boardId }}"></div>
@endsection

@push('scripts')
  @vite('resources/js/app.js')
@endpush
