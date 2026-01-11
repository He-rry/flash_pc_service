@extends('layouts.customers')
@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 text-center">
                    <h3 class="mb-4">Track Your Repair Status</h3>

                    <form action="{{ route('customer.track') }}" method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="phone" class="form-control" placeholder="Enter your phone number (e.g. 09...)" value="{{ request('phone') }}" required>
                            <button class="btn btn-primary" type="submit">Track Now</button>
                        </div>
                    </form>

                    @if($service)
                    <div class="mb-3">
                        <span class="d-block text-muted small">Customer Name</span>
                        <span class="fw-bold">{{ $service->customer_name }}</span>
                    </div>
                    <div class="mb-3">
                        <span class="d-block text-muted small">PC Model</span>
                        <span class="fw-bold">{{ $service->pc_model ?? 'N/A' }}</span>
                    </div>
                    <div class="mb-4">
                        <span class="d-block text-muted small">Service Type</span>
                        <span class="fw-bold text-primary">{{ $service->serviceType->service_name ?? 'General' }}</span>
                    </div>

                    @php
                    $status = $service->status->status_name ?? '';
                    $percent = 0;
                    $color = 'bg-secondary';
                    if($status == 'New') { $percent = 25; $color = 'bg-info'; }
                    elseif($status == 'On Going') { $percent = 50; $color = 'bg-primary'; }
                    elseif($status == 'Processing') { $percent = 75; $color = 'bg-warning'; }
                    elseif($status == 'Finished') { $percent = 100; $color = 'bg-success'; }
                    @endphp

                    <div class="track-wrapper">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small fw-bold">{{ $status }}</span>
                            <span class="small">{{ $percent }}%</span>
                        </div>
                        <div class="progress" style="height: 15px; border-radius: 10px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated {{ $color }}"
                                role="progressbar"
                                style="width: {{ $percent }}%;">
                            </div>
                        </div>
                    </div>
                    @elseif(request()->has('phone'))
                    <div class="alert alert-warning">ဖုန်းနံပါတ် မှားယွင်းနေပါသည်။ ထပ်မံကြိုးစားကြည့်ပါ။</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection