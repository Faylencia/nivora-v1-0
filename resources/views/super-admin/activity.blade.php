@extends('layouts.super-admin')

@section('title', 'Aktivitas - Super Admin Nivora')

@section('content')
    <div class="space-y-6"><x-page-header eyebrow="SUPER ADMIN" title="Aktivitas Platform" description="Pantau aktivitas penting dari seluruh toko." /><div class="ni-panel p-5"><div class="space-y-3">@foreach (['Admin baru ditambahkan ke Kopi Tuku','Profil Nusa Garden diperbarui','Rasa Rakyat mengajukan verifikasi toko'] as $activity)<div class="flex gap-3 rounded-2xl border border-[#eef3ff] bg-[#f7f9ff] p-4 text-sm text-[#5e647f]"><span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-[#687EFF]"></span>{{ $activity }}</div>@endforeach</div></div></div>
@endsection
