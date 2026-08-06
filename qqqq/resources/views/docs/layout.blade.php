<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Document' }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; color: #111; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; gap: 24px; }
        .brand { font-size: 20px; font-weight: 700; }
        .muted { color: #666; font-size: 12px; }
        .card { border: 1px solid #ddd; border-radius: 10px; padding: 16px; margin-top: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; vertical-align: top; }
        .right { text-align: right; }
        .total { font-size: 16px; font-weight: 700; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
            .card { border: none; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 12px;">
        <button onclick="window.print()">Print / Save as PDF</button>
    </div>

    @yield('content')
</body>
</html>
