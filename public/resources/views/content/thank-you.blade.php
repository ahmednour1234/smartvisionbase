@extends('web.layouts.app')

@section('title', 'Thank You')

@section('content')
    {{-- تحويل فوري بدون انتظار --}}
    <meta http-equiv="refresh" content="0;url={{ $nextUrl }}">

    <div class="container py-5 text-center">
        {{-- هنا ممكن تحط أي أكواد تتبّع/بيكسل، الصفحة لن تبقى للمستخدم ولكن ستمرّ عليها --}}
        <h1 class="mb-3">Thank you!</h1>
        <p class="mb-4">You will be redirected shortly...</p>

        {{-- JS Redirect (أقوى من الميتا ريـفريش) --}}
        <script>
            (function () {
                try {
                    window.location.replace(@json($nextUrl));
                } catch (e) {
                    window.location.href = @json($nextUrl);
                }
            })();
        </script>

        <noscript>
            <a class="btn btn-primary" href="{{ $nextUrl }}">Continue</a>
        </noscript>
    </div>
@endsection
