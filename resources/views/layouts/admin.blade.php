@extends('tailadmin::layouts.app')

@section('title', $title ?? 'Admin')

@section('content')
    @yield('content')
@endsection

@push('scripts')
@php
    $lockMinutes = (int) config('session.lifetime', 120) / 60;
    $warningSeconds = 60;
    $totalSeconds = $lockMinutes * 60;
@endphp
@if ($lockMinutes > 0 && $warningSeconds > 0 && $totalSeconds > $warningSeconds)
<script>
(function() {
    const LOCK_MINUTES = {{ $lockMinutes }};
    const WARNING_SECONDS = {{ $warningSeconds }};
    const TOTAL_SECONDS = LOCK_MINUTES * 60;
    const WARNING_START = TOTAL_SECONDS - WARNING_SECONDS;
    if (TOTAL_SECONDS < WARNING_SECONDS || WARNING_SECONDS <= 0 || typeof Swal === 'undefined') return;
    let idleSeconds = 0, warningShown = false, countdownInterval = null;
    function performLogout() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        fetch("{{ route('logout') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: '_token=' + encodeURIComponent(csrfToken)
        }).then(() => { window.location.href = "{{ route('login') }}"; }).catch(() => { window.location.href = "{{ route('login') }}"; });
    }
    function resetIdle() { if (!warningShown) idleSeconds = 0; }
    function showTimeoutModal() {
        warningShown = true;
        let remaining = WARNING_SECONDS;
        Swal.fire({
            icon: 'warning',
            title: 'Are you still there?',
            html: '<p class="text-sm text-gray-700 mb-4">You will be logged out in <strong><span id="swal-time-remaining">' + remaining + '</span> second(s)</strong> due to inactivity.</p><p class="text-xs text-gray-500">Confirm you are still working to keep your session active.</p>',
            showCancelButton: true,
            confirmButtonText: 'Stay Logged In',
            cancelButtonText: 'Lock Now',
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            clearInterval(countdownInterval);
            if (result.isConfirmed) { idleSeconds = 0; warningShown = false; }
            else if (result.dismiss !== Swal.DismissReason.timer) performLogout();
        });
        const timeEl = document.getElementById('swal-time-remaining');
        countdownInterval = setInterval(() => {
            remaining--;
            if (remaining <= 0) { clearInterval(countdownInterval); Swal.close(); performLogout(); }
            else if (timeEl) timeEl.textContent = remaining;
        }, 1000);
    }
    ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach((evt) => window.addEventListener(evt, resetIdle, { passive: true }));
    setInterval(() => {
        idleSeconds++;
        if (!warningShown && idleSeconds >= WARNING_START && idleSeconds < TOTAL_SECONDS) showTimeoutModal();
        else if (idleSeconds >= TOTAL_SECONDS) performLogout();
    }, 1000);
})();
</script>
@endif
@endpush
