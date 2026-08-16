{{-- resources/views/dashboard/marketing_refs/index.blade.php --}}
@extends('layouts.layoutMaster')

@section('title', __('Registrations.title'))

@push('styles')
    {{-- Font Awesome (icons) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* ثيم برتقالي بسيط */
        .btn { display:inline-flex; align-items:center; gap:.5rem; border-radius:.5rem; padding:.55rem .9rem; font-weight:600; }
        .btn-orange { background:#ea580c; color:#fff; }
        .btn-orange:hover { background:#c2410c; color:#fff; }
        .btn-orange-outline { border:1px solid #ea580c; color:#ea580c; background:#fff; }
        .btn-orange-outline:hover { background:#fff7ed; }
        .btn-muted { border:1px solid #e5e7eb; background:#fff; color:#111827; }
        .btn-muted:hover { background:#f9fafb; }

        /* أزرار أيقونة فقط */
        .icon-only { padding:.55rem; width:2.5rem; justify-content:center; }
        .chip-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
        }
        .badge { display:inline-flex; align-items:center; gap:.35rem; font-size:.75rem; border-radius:999px; padding:.25rem .55rem; }
        .badge-orange { background:#ffedd5; color:#9a3412; } /* نشط */
        .badge-gray { background:#f3f4f6; color:#4b5563; }   /* متوقف */

        .table thead th { font-size:.75rem; text-transform:uppercase; color:#6b7280; font-weight:700; }
        .table tbody td { font-size:.9rem; color:#111827; }
        .icon-left { position:absolute; left:.75rem; top:50%; transform:translateY(-50%); color:#9ca3af; }
        .input-icon { padding-left:2.25rem; }
    </style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto p-6 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold">الروابط التسويقية</h1>
            <p class="text-gray-600 text-sm">تتبّع مرجع التسجيل من حملات التسويق فقط (بدون مبيعات)</p>
        </div>
    </div>

    {{-- Search + Add in the SAME line --}}
    <div class="bg-white rounded-md p-4 border">
   

            {{-- push Add to the far side but in the same line --}}
            <div class="grow"></div>

            <a href="{{ route('marketing-refs.create') }}" class="btn btn-orange" title="إضافة مرجع">
                <i class="fa-solid fa-plus"></i>
                <span>إضافة مرجع</span>
                </a>
        
    </div>

    {{-- Table --}}
    <div class="table-responsive">
      <table class="table text-center align-middle">
            <thead class="bg-gray-50">
                <tr class="text-right">
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">الاسم</th>
                    <th class="px-4 py-3">الكود</th>
                    <th class="px-4 py-3">الحالة</th>
                    <th class="px-4 py-3">الاستخدامات</th>
                    <th class="px-4 py-3">ينتهي في</th>
                    <th class="px-4 py-3">أُنشئ</th>
                    <th class="px-4 py-3 text-left">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($refs as $ref)
                    @php $share = url('/register').'?ref='.$ref->code; @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $ref->id }}</td>
                        <td class="px-4 py-3 text-sm font-medium">{{ $ref->name }}</td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-1 border rounded bg-gray-50 text-gray-800 chip-code">{{ $ref->code }}</span>

                                {{-- Copy / Preview (أيقونات فقط) --}}
                                <button type="button" data-copy="{{ $share }}"
                                        class="btn btn-orange-outline icon-only copy-btn" title="نسخ الرابط">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                                <a href="{{ $share }}" target="_blank"
                                   class="btn btn-muted icon-only" title="معاينة">
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if($ref->active)
                                <span class="badge badge-orange"><i class="fa-solid fa-circle-check"></i> نشط</span>
                            @else
                                <span class="badge badge-gray"><i class="fa-regular fa-circle-xmark"></i> متوقف</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if(!is_null($ref->max_uses))
                                <span class="font-semibold">{{ $ref->uses_count }}</span>
                                <span class="text-gray-400">/</span>
                                <span class="text-gray-600">{{ $ref->max_uses }}</span>
                            @else
                                <span class="font-semibold">{{ $ref->uses_count }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">{{ $ref->expires_at?->format('Y-m-d H:i') ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm">{{ $ref->created_at?->format('Y-m-d') }}</td>

                        {{-- Actions: Edit / Delete (أيقونات فقط) --}}
                        <td class="px-4 py-3 text-left">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('marketing-refs.edit', $ref) }}"
                                   class="btn btn-orange icon-only" title="تعديل">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('marketing-refs.destroy', $ref) }}" method="POST"
                                      onsubmit="return confirm('حذف المرجع؟ لا يمكن التراجع.');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-orange-outline icon-only" title="حذف">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10">
                            <div class="text-center text-gray-500">
                                <i class="fa-regular fa-clipboard fa-2x mb-2"></i>
                                لا توجد بيانات حتى الآن. ابدأ بإضافة مرجع جديد.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="px-4 py-4">
            {{ $refs->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<script>
document.addEventListener('click', function(e){
    const btn = e.target.closest('.copy-btn');
    if (btn) {
        const txt = btn.getAttribute('data-copy');
        navigator.clipboard.writeText(txt).then(() => {
            const prev = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-check"></i>';
            setTimeout(()=> btn.innerHTML = prev, 1200);
        });
    }
});
</script>
@endsection
