@if (session('exception_message'))
    <div class="alert alert-danger">
        {{ session('exception_message') }}
    </div>
@endif