@extends('layouts.super-admin')

@section('title', 'Admin Toko - Super Admin Nivora')

@section('content')
    <div class="space-y-6">
        <x-page-header eyebrow="SUPER ADMIN" title="Admin bisnis" description="Akun dengan peran owner dan bisnis yang terhubung.">
            <a href="{{ route('super-admin.business-applications') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#687EFF] px-4 text-sm font-semibold text-white">Tinjau calon owner</a>
        </x-page-header>
        <section class="ni-panel overflow-hidden">
            @if ($admins->isEmpty())
                <div class="p-10 text-center"><p class="font-semibold">Belum ada akun owner.</p><p class="mt-1 text-sm text-[#5e647f]">Akun dengan role owner akan tampil setelah terdaftar.</p></div>
            @else
                <div class="overflow-x-auto"><table class="ni-table"><thead><tr><th>Nama</th><th>Email</th><th>Bisnis terhubung</th><th>Status akun</th><th></th></tr></thead><tbody>
                    @foreach ($admins as $admin)
                        <tr class="border-b border-[#eef3ff]"><td class="py-3 pr-4 font-semibold text-[#1f2544]">{{ $admin->name }}</td><td class="py-3 pr-4">{{ $admin->email }}</td><td class="py-3 pr-4">{{ $admin->businesses->pluck('name')->join(', ') ?: '—' }}</td><td class="py-3 pr-4">{{ $admin->status ?: '—' }}</td><td class="py-3 text-right"><a href="{{ route('super-admin.store-admins.show', ['admin' => $admin->id]) }}" class="mr-3 font-semibold text-[#687EFF]">Detail</a><a href="{{ route('super-admin.store-admins.edit', ['admin' => $admin->id]) }}" class="font-semibold text-[#5e647f]">Edit</a></td></tr>
                    @endforeach
                </tbody></table></div>
                <div class="border-t border-[#e7ebff] p-4">{{ $admins->links() }}</div>
            @endif
        </section>
    </div>
@endsection
