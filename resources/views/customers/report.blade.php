@extends('layouts.customers')
@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4 text-primary">PC Repair Service Report</h2>

                    @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('customer.report.submit') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">နာမည်</label>
                                <input type="text" name="customer_name" class="form-control" placeholder="မောင်မောင်" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">ဖုန်းနံပါတ်</label>
                                <input type="text" name="customer_phone" class="form-control" placeholder="09xxxxxxx" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">နေရပ်လိပ်စာ</label>
                            <input type="text" name="customer_address" class="form-control" placeholder="မြို့နယ်၊ လမ်း၊ အိမ်အမှတ်" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">📍 သင့်တည်နေရာကို မြေပုံပေါ်တွင် ရွေးချယ်ပါ</label>
                            <div id="map" style="height: 300px; border-radius: 10px;" class="shadow-sm"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small">Latitude</label>
                                <input type="text" name="lat" id="lat" class="form-control bg-light" readonly required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small">Longitude</label>
                                <input type="text" name="long" id="long" class="form-control bg-light" readonly required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Service အမျိုးအစား</label>
                            <select name="service_type_id" class="form-select" required>
                                <option value="" disabled selected>အမျိုးအစား ရွေးချယ်ပါ</option>
                                @if(isset($service_types) && $service_types->count())
                                @foreach($service_types as $service_type)
                                <option value="{{ $service_type->id }}">{{ $service_type->service_name }}</option>
                                @endforeach
                                @else
                                <option value="" disabled>မည်သည့် အမျိုးအစားမျှ မရှိပါ</option>
                                @endif
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">PC Model</label>
                            <input type="text" name="pc_model" class="form-control" placeholder="e.g. Dell XPS 15">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">ဖြစ်နေသည့် ပြဿနာ</label>
                            <textarea name="issue_description" class="form-control" rows="4" placeholder="ဖြစ်နေတဲ့ ပြဿနာကို အသေးစိတ် ရေးပေးပါ..." required></textarea>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">Submit Report</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script>
    // Global variable
    var map = null;

    window.addEventListener('load', function() {
        if (map !== null) {
            map.remove();
        }
        //မြေပုံကို စတင်သတ်မှတ်
        map = L.map('map').setView([16.8661, 96.1951], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var currentMarker;

        // ၃။ Click Event
        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;

            if (currentMarker) {
                map.removeLayer(currentMarker);
            }

            currentMarker = L.marker([lat, lng]).addTo(map);

            // Input fieldထဲတန်ဖိုးဖြည့်
            document.getElementById('lat').value = lat.toFixed(6);
            document.getElementById('long').value = lng.toFixed(6);
        });
    });
</script>