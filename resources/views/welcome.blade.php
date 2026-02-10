@extends('layouts.customers')

@section('content')
<div class="container py-5 text-center">
    <h1 class="display-4 fw-bold mb-5">Make Your PC Service Experience Better</h1>
    <div class="row g-4 justify-content-center">
        <div class="col-md-5">
            <div class="card h-100 shadow border-0">
                <div class="card-body p-5">
                    <div class="display-1 text-primary mb-3">👤</div>
                    <h3>Customer Portal</h3>
                    <p class="text-muted">သင့် PC ပြဿနာကို report တင်ရန် သို့မဟုတ် ပြင်ဆင်မှုအခြေအနေကို စစ်ဆေးရန်</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('customer.report') }}" class="btn btn-primary">Report an Issue</a>
                        <a href="{{ route('customer.track') }}" class="btn btn-outline-primary">Track My Service</a>
                    </div>
                </div>
            </div>
        </div>
        <footer class="mt-5 py-3 text-center">
            <p class="text-muted" style="font-size: 0.8rem;">
                © 2026 PC Service Pro. All rights reserved.
                <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-secondary ms-2" style="opacity: 0.5;">🔐</a>
            </p>
        </footer>
        @endsection