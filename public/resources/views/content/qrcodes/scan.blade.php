@extends('layouts.layoutMaster')

@section('title', 'QR Scanner')

@section('content')

<style>
    .qr-container {
        background-color: #f4f6f8;
        padding: 40px 25px;
        border-radius: 15px;
        max-width: 500px;
        margin: 50px auto;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        text-align: center;
    }

    .qr-title {
        font-size: 22px;
        font-weight: bold;
        margin-bottom: 20px;
        color: #2c3e50;
    }

    .qr-box {
        border: 3px dashed #3498db;
        border-radius: 10px;
        padding: 15px;
        background-color: #fff;
        margin-bottom: 20px;
    }

    .scanner-input {
        margin-top: 15px;
    }

    .qr-result {
        margin-top: 25px;
    }
</style>

<div class="qr-container">
    <div class="qr-title">📷 امسح رمز QR للدخول</div>

    <div id="reader" class="qr-box"></div>

    <div class="scanner-input">
        <label for="qr-code-input" class="form-label">أو امسح بالكاميرا أو المكنة 👇</label>
        <input type="text" id="qr-code-input" class="form-control text-center" placeholder="ضع المؤشر هنا وامسح بالكود" autofocus>
    </div>

    <div id="result" class="qr-result"></div>
</div>

{{-- مكتبة قراءة QR --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    // دالة لعرض النتيجة
    function showMessage(status, message) {
        const resultDiv = document.getElementById('result');
        let alertClass = 'info';

        if (status === 'success') alertClass = 'success';
        else if (status === 'warning' || status === 'scanned') alertClass = 'warning';
        else alertClass = 'danger';

        resultDiv.innerHTML = `
            <div class="alert alert-${alertClass}">
                <strong>${message}</strong>
            </div>
        `;
    }

    // إرسال الكود إلى السيرفر
    function sendCodeToServer(code) {
        fetch("{{ route('qr.scan.check') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ code })
        })
        .then(async res => {
            const contentType = res.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                return res.json();
            } else {
                throw new Error("الرد ليس JSON.");
            }
        })
        .then(data => {
            showMessage(data.status || 'info', data.message || 'تم التحقق.');
        })
        .catch(err => {
            showMessage('error', 'حدث خطأ أثناء الاتصال بالسيرفر.');
            console.error(err);
        });
    }

    // ✅ طريقة الكاميرا
    function onScanSuccess(decodedText, decodedResult) {
        html5QrcodeScanner.clear(); // إيقاف الكاميرا بعد أول مسح
        sendCodeToServer(decodedText);
    }

    const html5QrcodeScanner = new Html5QrcodeScanner(
        "reader",
        { fps: 10, qrbox: 250 },
        false
    );

    html5QrcodeScanner.render(onScanSuccess);

    // ✅ طريقة المكنة (Keyboard Scanner)
    document.getElementById('qr-code-input').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            let code = e.target.value.trim();
            if (code !== '') {
                sendCodeToServer(code);
                e.target.value = ''; // تفريغ الحقل بعد الاستخدام
            }
        }
    });
</script>

@endsection
