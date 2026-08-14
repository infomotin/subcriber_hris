@extends('layouts.system_admin')

@section('title', 'ZKTeco ADMS নেটওয়ার্ক সেটিংস')

@section('content')
<style>
    .status-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 6px; }
    .status-dot.online { background: #22c55e; box-shadow: 0 0 6px rgba(34,197,94,0.5); }
    .status-dot.offline { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,0.5); }
    .info-card { border-left: 4px solid #4f46e5; }
    .toggle-switch { position: relative; width: 44px; height: 24px; display: inline-block; flex-shrink: 0; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: #d1d5db; border-radius: 24px; transition: 0.3s; }
    .toggle-slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background: #fff; border-radius: 50%; transition: 0.3s; }
    .toggle-switch input:checked + .toggle-slider { background: #4f46e5; }
    .toggle-switch input:checked + .toggle-slider:before { transform: translateX(20px); }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 no-print">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-wifi text-warning me-2 font-size-22"></i> ZKTeco ADMS নেটওয়ার্ক সার্ভার সেটিংস</h4>
        <p class="text-muted font-size-13 mb-0">ADMS লিস্টেনার পোর্ট, সার্ভার গেটওয়ে IP, ডিভাইস হার্টবিট ইন্টারভেল কনফিগার করুন এবং সংযুক্ত বায়োমেট্রিক মেশিনের অবস্থা দেখুন।</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Network Settings Form -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-slider-alt text-primary me-2"></i> লিস্টেনার ও সার্ভার কনফিগারেশন</h5>
                    </div>
                    <div class="card-body p-4">
<div class="alert alert-info border-0 mb-4 p-3 rounded-3">
                            <i class="bx bxl-zoom-meet text-info me-2"></i>
                            <strong>ADMS কীভাবে কাজ করে?</strong>
                            <p class="mb-0 mt-1 font-size-12">
                                <strong>ADMS</strong> = <em>Attendance Device Management System</em> (উপস্থিতি ডিভাইস ম্যানেজমেন্ট সিস্টেম)। এটি এই সার্ভারে চলে এমন একটি লিস্টেনার (Listener) সার্ভিস যা ZKTeco বায়োমেট্রিক ডিভাইস থেকে রিয়েল-টাইম উপস্থিতি ডেটা গ্রহণ করে। যখন একজন কর্মচারী তার ফিঙ্গারপ্রিন্ট বা মুখ স্ক্যান করে, তখন ডিভাইস স্বয়ংক্রিয়ভাবে এই সার্ভারে ডেটা পাঠায় — কোনো ম্যানুয়াল আপলোড দরকার নেই।
                            </p>
                            <div class="mt-2 font-size-11">
                                <strong>ডেটা ফ্লো (Data Flow):</strong><br>
                                <code>কর্মচারী ফিঙ্গারপ্রিন্ট স্ক্যান করে</code> → <code>ডিভাইস HTTP POST পাঠায় → http://সার্ভার_IP:পোর্ট/iclock/cdata</code> → <code>সার্ভার attendance_logs তে সংরক্ষণ করে</code> → <code>ব্যাকগ্রাউন্ড স্ক্রিপ্ট raw_punch_data তে সিঙ্ক করে</code> → <code>উপস্থিতি পদ্ধতিতে প্রক্রিয়া করে বেতনে যুক্ত করে</code>
                            </div>
                        </div>

                        <form action="{{ route('admin.system.network.update') }}" method="POST">
                            @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-size-13">ADMS Server Port</label>
                        <input type="number" name="adms_port" class="form-control border-secondary" value="{{ old('adms_port', $setting->adms_port) }}" min="80" max="65535" required>
                        <small class="text-muted"><strong> এটি কী?</strong> এটি সার্ভারের সেই পোর্ট যেখানে ZKTeco ডিভাইসগুলো উপস্থিতি ডেটা পাঠাতে সংযোগ করে।<br>
                        <strong>কীভাবে কাজ করে:</strong> যখন কর্মচারী ফিঙ্গারপ্রিন্ট/মুখ স্ক্যান করে, ডিভাইস একটি HTTP POST request পাঠায়:<br>
                        <code>http://GATEWAY_IP:PORT/iclock/cdata</code><br>
                        সার্ভার ডেটা গ্রহণ করে <code>attendance_logs</code> 테이블ে সংরক্ষণ করে।<br><br>
                        <strong>ডিফল্ট: পোর্ট 80</strong> — স্ট্যান্ডার্ড HTTP পোর্ট। বেশীরভাগ ZKTeco ডিভাইস আগেই পোর্ট 80-এ সেট করা আছে।<br>
                        <strong>কখন 8000 ব্যবহার করবেন?</strong> শুধুমাত্র যখন সার্ভারে পোর্ট 80 ট্রাফিক ব্লক করা আছে বা একই সার্ভারে একাধিক সার্ভিস চলে।</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-size-13">Server Gateway Host / IP</label>
                        <input type="text" name="gateway_ip" class="form-control border-secondary" value="{{ old('gateway_ip', $setting->gateway_ip) }}" required>
                        <small class="text-muted"><strong>এটি কী?</strong> এই সার্ভারের সেই <strong>পাবলিক ঠিকানা</strong> যেটি ZKTeco ডিভাইস ব্যবহার করে এই সার্ভারে যোগাযোগ করে। এটি যেন সার্ভারের "ফোন নং" — ডিভাইসগুলো এই IP-তে কল করে।<br>
                        <strong>কীভাবে কাজ করে:</strong> ডিভাইসগুলোকে ADMS সেটিংসে এই IP অ্যাড্রেস দিতে হয়। যখন ডিভাইস কানেক্ট করতে চায়, এটি এই IP-তে POST request পাঠায়।<br>
                        <strong>উদাহরণ:</strong> <code>15.235.229.40</code> বা <code>https://hr.nexogiant.com</code><br>
                        <strong>ZKTeco ডিভাইসের জন্য:</strong> ডিভাইসের ADMS সেটিংসে "Server IP" হিসেবে এই মান এবং "Server Port" হিসেবে উপরের ADMS পোর্ট মান সেট করুন।</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark font-size-13">Device Heartbeat Interval (Seconds)</label>
                        <input type="number" name="push_interval" class="form-control border-secondary" value="{{ old('push_interval', $setting->push_interval) }}" min="5" max="3600" required>
                        <small class="text-muted"><strong>এটি কী?</strong> প্রতিটি ZKTeco ডিভাইস নিয়মিত সার্ভারে "হার্টবিট" (ping) পাঠায় — এটি নিশ্চিত করে যে ডিভাইস এখনও অনলাইন এবং সংযুক্ত আছে।<br>
                        <strong>কীভাবে কাজ করে:</strong> ডিভাইস প্রতি X সেকেন্ডে একটি ছোট HTTP request পাঠায়। যদি সার্ভার 2× এই সময়ের মধ্যে কোনো পিং না পায়, ডিভাইসটি <strong>OFFLINE</strong> হিসেবে দেখায় (<span class="status-dot offline"></span>)।<br>
                        <strong>ডিফল্ট: 30 সেকেন্ড</strong> — দ্রুতly অবলাইন ডিভাইস শনাক্ত করতে যথেষ্ট ছোট, কিন্তু অতিরিক্ত ট্রাফিক কম।<br>
                        <strong>রেঞ্জ:</strong> 5 সেকেন্ড (খুব দ্রুত) থেকে 3600 সেকেন্ড (1 ঘণ্টা, কম ট্রাফিকের জন্য)।</small>
                    </div>

                    <div class="mb-4 d-flex align-items-center justify-content-between p-3 rounded-3 border bg-light">
                        <div>
                            <label class="form-label fw-bold text-dark mb-0">ADMS গেটওয়ে চালু করুন</label>
                            <small class="text-muted"><strong>বন্ধ</strong> থাকলে, ডিভাইসগুলো সার্ভারে ডেটা পাঠতে পারবে না। আগে থেকে যে ডেটা আছে সেটা ক্ষতি হয় না।</small>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_adms_active" value="1" {{ $setting->is_adms_active ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-warning fw-bold px-4 text-dark">
                        <i class="bx bx-save me-1"></i> নেটওয়ার্ক কনফিগারেশন আপডেট করুন
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Active ADMS Connected Devices List -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bx bx-chip text-primary me-2"></i> সংযুক্ত বায়োমেট্রিক মেশিন <span class="badge bg-info font-size-10">{{ $activeDevices->count() }}টি মোট</span></h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 font-size-12">
                        <thead class="bg-light">
                            <tr>
                                <th>অবস্থা</th>
                                <th>সিরিয়াল নং</th>
                                <th>নাম</th>
                                <th>ডিভাইস IP</th>
                                <th>টেন্যান্ট</th>
                                <th>শেষ দেখা</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeDevices as $dev)
                                <tr>
                                    <td>
                                        <span class="status-dot {{ $dev->is_online ? 'online' : 'offline' }}"></span>
                                        <span class="badge {{ $dev->is_online ? 'bg-success' : 'bg-danger' }}">
                                            {{ $dev->is_online ? 'ONLINE' : 'OFFLINE' }}
                                        </span>
                                    </td>
                                    <td><code class="fw-bold text-dark">{{ $dev->serial_number }}</code></td>
                                    <td>{{ $dev->name ?? '—' }}</td>
                                    <td><small class="text-muted">{{ $dev->ip_address ?? 'Dynamic' }}</small></td>
                                    <td><span class="badge bg-secondary">{{ $dev->tenant->name ?? 'Unassigned' }}</span></td>
                                    <td><small class="text-muted">{{ $dev->last_heartbeat ? $dev->last_heartbeat->diffForHumans() : 'Never' }}</small></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">No ZKTeco biometric devices registered in system.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Connection Info Card -->
<div class="card border-info info-card mb-4 no-print">
    <div class="card-body p-3">
        <div class="d-flex align-items-start gap-3">
            <i class="bx bx-info-circle text-info font-size-20 mt-1"></i>
            <div>
                <strong class="text-info">ADMS Endpoint URL</strong>
                <code class="ms-2">http://{{ $setting->gateway_ip }}:{{ $setting->adms_port }}/iclock/cdata</code>
                <p class="text-muted font-size-11 mt-1 mb-0">
                    Use this URL + Token in ZKTeco device ADMS settings for real-time attendance sync.
                    Devices send HTTP POST requests to this endpoint. Gateway must be active for data to flow.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
