@extends('layouts.guest_app')

@section('content')
    <div class="wrapper">
        <edit-booking-form :details="{{ json_encode($details) }}"></edit-booking-form>
    </div>
@endsection
