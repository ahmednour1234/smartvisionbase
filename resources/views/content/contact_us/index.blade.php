@extends('layouts.layoutMaster')

@section('title', __('contact.index_title'))

@section('content')
<div class="container-fluid">
    <h4>{{ __('contact.index_title') }}</h4>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label>{{ __('contact.from_date') }}</label>
            <input type="date" name="from" value="{{ request('from') }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label>{{ __('contact.to_date') }}</label>
            <input type="date" name="to" value="{{ request('to') }}" class="form-control">
        </div>
        <div class="col-md-2 align-self-end">
            <button type="submit" class="btn btn-primary">{{ __('contact.filter') }}</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>{{ __('contact.name') }}</th>
                    <th>{{ __('contact.title') }}</th>
                    <th>{{ __('contact.email') }}</th>
                    <th>{{ __('contact.phone') }}</th>
                    <th>{{ __('contact.message') }}</th>
                    <th>{{ __('contact.date') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($contacts as $contact)
                    <tr>
                        <td>{{ $contact->name }}</td>
                        <td>{{ $contact->title }}</td>
                        <td>{{ $contact->email }}</td>
                        <td>{{ $contact->phone }}</td>
                        <td>{{ $contact->message }}</td>
                        <td>{{ $contact->created_at->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">{{ __('contact.no_data') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $contacts->links() }}
    </div>
</div>
@endsection
