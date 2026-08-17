@extends('layouts.admin')

@section('content')
    @include('admin.partials.flash')

    <div class="card overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Type</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Channel</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Recipient</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Status</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Sent at</th>
                    <th class="px-4 py-3 text-right font-medium text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($notifications as $notification)
                    <tr>
                        <td class="px-4 py-3 font-medium text-brand-900">{{ $notification->type }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $notification->channel?->value ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $notification->recipient ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $notification->status ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $notification->sent_at?->format('d M Y H:i') ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.notifications.show', $notification, absolute: false) }}" class="text-brand-700 hover:text-brand-900">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">No notifications found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $notifications->links() }}</div>
@endsection
