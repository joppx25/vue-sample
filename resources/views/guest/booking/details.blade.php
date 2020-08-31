@extends('layouts.guest_app')

@section('content')
    <div class="wrapper">
        <booking-details :details="{{ json_encode($details) }}"></booking-details>
    </div>
@endsection
