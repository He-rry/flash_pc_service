@extends('layouts.customers')

@section('content')
<style>
    .admin-login-link {
        opacity: 0.3;
        transition: opacity 0.3s ease;
    }

    .admin-login-link:hover {
        opacity: 1;
        color: #6c757d;
    }
</style>
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
    </div>

    <footer class="mt-5 py-3 text-center">
        <p class="text-muted" style="font-size: 0.8rem;">
            © 2026 PC Service Pro. All rights reserved.

            @auth
            {{-- Login ဝင်ထားလျှင် Admin ဟုတ်မဟုတ် စစ်ဆေးမည် --}}
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin'))
            <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-secondary ms-2 admin-login-link">🔐</a>
            @else
            {{-- Admin မဟုတ်လျှင် Home သို့သာ ပြန်ပို့မည် --}}
            <a href="{{ url('/') }}" class="text-decoration-none text-secondary ms-2 admin-login-link">🔐</a>
            @endif
            @else
            {{-- Login မဝင်ရသေးလျှင် Login Page သို့ ပို့မည် --}}
            <a href="{{ route('login') }}" class="text-decoration-none text-secondary ms-2 admin-login-link">🔐</a>
            @endauth
        </p>
    </footer>
</div>

@endsection