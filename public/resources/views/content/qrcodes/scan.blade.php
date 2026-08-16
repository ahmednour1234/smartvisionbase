@extends('layouts.layoutMaster')

@section('title', 'QR Scanner')

@section('content')
<style>
    .qr-container {
        background-color: #f9f9f9;
        padding: 40px 20px;
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        max-width: 400px;
        margin: 50px auto;
    }

    .qr-title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 20px;
        color: #2c3e50;
    }

    .qr-box {
        border: 3px dashed #3498db;
        border-radius: 10px;
        padding: 15px;
        background-color: #fff;
    }

    .qr-result {
        margin-top: 25px;
    }
</style>

<div class="qr-container text-center">
    <div class="qr-title">📷 امسح رمز QR للدخول</div>

    <div id="reader" class="qr-box"></div>

    <div id="result" class="qr-result"></div>
</div>

{{-- مكتبة قراءة QR --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    function showMessage(status, message) {
        const resultDiv = document.getElementById('result');
        let alertClass = 'info';

        if (status === 'success') alertClass = 'success';
        else if (status === 'scanned') alertClass = 'warning';
        else alertClass = 'danger';

        resultDiv.innerHTML = `
            <div class="alert alert-${alertClass} mt-3">
                <strong>${message}</strong>
            </div>
        `;
    }

    function onScanSuccess(decodedText, decodedResult) {
        html5QrcodeScanner.clear();

        fetch("{{ route('qr.scan.check') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ code: decodedText })
        })
        .then(async res => {
            const contentType = res.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                return res.json();
            } else {
                throw new Error("الرد ليس JSON. تأكد من أن الراوت يرجع response()->json().");
            }
        })
        .then(data => {
            showMessage(data.status || 'info', data.message || 'تم الرد بدون رسالة.');
        })
        .catch(err => {
            showMessage('error', 'حدث خطأ أثناء الاتصال بالسيرفر.');
            console.error(err);
        });
    }

    const html5QrcodeScanner = new Html5QrcodeScanner(
        "reader", { fps: 10, qrbox: 250 }, false
    );

    html5QrcodeScanner.render(onScanSuccess);
</script>
@endsection
