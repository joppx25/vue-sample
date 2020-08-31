@extends('layouts.guest_app')

@section('content')
    <div class="steps">
        <div class="steps-breadcrumb">
            <a href="javascript:void(0);" class="step-item step-1 active"><b>STEP.1</b><br>@lang('labels.event')</a>
            <a href="javascript:void(0);" class="step-item step-2"><b>STEP.2</b><br>@lang('labels.booking')</a>
            <a href="javascript:void(0);" class="step-item step-3"><b>STEP.3</b><br>@lang('labels.done')</a>
        </div>
    </div>
    <div class="wrapper">
        <guest-event-form :event-id="{{ $id }}"></guest-event-form>
    </div>
@endsection
