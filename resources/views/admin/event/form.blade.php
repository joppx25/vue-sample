@extends('layouts.admin_app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12" style="margin-bottom: 18px;">
                <h4 class="form-title">{{ __('labels.event') }}</h4>
            </div>
            <div class="col-12">
                <event-form-field :event="{{ empty($event) ? json_encode('') : json_encode($event) }}" :schedules="{{ empty($schedules) ? json_encode([]) : json_encode($schedules) }}" :clone-details="{{ $eventDetails ?? 'null' }}" :staffs="{{ empty($staffs) ? json_encode('') : json_encode($staffs)}}"/>
            </div>
        </div>
    </div>
@endsection
