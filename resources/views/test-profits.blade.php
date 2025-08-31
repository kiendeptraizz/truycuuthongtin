@extends('layouts.admin')

@section('title', 'Test Profits')

@section('content')
<div class="container-fluid">
    <h1>Test Profits Page</h1>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Test AJAX Calls</h5>
                </div>
                <div class="card-body">
                    <button onclick="testOrders()" class="btn btn-primary">Test Orders</button>
                    <button onclick="testStats()" class="btn btn-secondary">Test Statistics</button>
                    <div id="results" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function testOrders() {
    console.log('Testing orders...');
    $('#results').html('Loading orders...');
    
    $.ajax({
        url: '{{ route("admin.profits.today-orders") }}',
        method: 'GET',
        success: function(response) {
            console.log('Orders response:', response);
            $('#results').html('<h6>Orders Success:</h6><pre>' + JSON.stringify(response, null, 2) + '</pre>');
        },
        error: function(xhr) {
            console.error('Orders error:', xhr);
            $('#results').html('<h6>Orders Error:</h6><pre>' + xhr.status + ' ' + xhr.statusText + '</pre>');
        }
    });
}

function testStats() {
    console.log('Testing statistics...');
    $('#results').html('Loading statistics...');
    
    $.ajax({
        url: '{{ route("admin.profits.today-statistics") }}',
        method: 'GET',
        success: function(response) {
            console.log('Statistics response:', response);
            $('#results').html('<h6>Statistics Success:</h6><pre>' + JSON.stringify(response, null, 2) + '</pre>');
        },
        error: function(xhr) {
            console.error('Statistics error:', xhr);
            $('#results').html('<h6>Statistics Error:</h6><pre>' + xhr.status + ' ' + xhr.statusText + '</pre>');
        }
    });
}

$(document).ready(function() {
    console.log('Test page ready');
    
    // Setup CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});
</script>
@endsection
