@extends('layouts.layoutMaster')

@section('title', __('pdfs.title'))

@section('content')
<div class="container">
    <h2 class="mb-4">{{ __('pdfs.title') }}</h2>

    <a href="{{ route('pdfs.create') }}" class="btn btn-primary mb-3">{{ __('pdfs.add_new') }}</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>{{ __('pdfs.name') }}</th>
                <th>{{ __('pdfs.link') }}</th>
                <th>{{ __('pdfs.status') }}</th>
                <th>{{ __('pdfs.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pdfs as $pdf)
                <tr>
                    <td>{{ $pdf->name }}</td>
                    <td>
                        <div class="d-flex flex-column">
                            <input type="text" class="form-control link-input mb-1" value="{{ asset('public/'.$pdf->pdf) }}" readonly>
                            <button class="btn btn-sm btn-outline-secondary copy-btn" data-link="{{ asset('public/'.$pdf->pdf) }}">
                                {{ __('pdfs.copy_link') }}
                            </button>
                        </div>
                    </td>
                    <td>
                        @if($pdf->active)
                            <span class="badge bg-success">{{ __('pdfs.active') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('pdfs.inactive') }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('pdfs.edit', $pdf) }}" class="btn btn-sm btn-info">{{ __('pdfs.edit') }}</a>

                        <form action="{{ route('pdfs.destroy', $pdf) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('pdfs.confirm_delete') }}')">
                                {{ __('pdfs.delete') }}
                            </button>
                        </form>

                        <form action="{{ route('pdfs.toggle-active', $pdf) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning">
                                {{ $pdf->active ? __('pdfs.deactivate') : __('pdfs.activate') }}
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.copy-btn').forEach(function(button) {
            button.addEventListener('click', function () {
                const link = this.getAttribute('data-link');
                navigator.clipboard.writeText(link).then(() => {
                    alert("{{ __('pdfs.link_copied') }}");
                });
            });
        });
    });
</script>
