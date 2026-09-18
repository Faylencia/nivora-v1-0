@props([
    'headers' => [],
    'rows' => [],
    'emptyMessage' => 'Tidak ada data.',
])

<div class="overflow-hidden rounded-[26px] border border-[#e7ebff] bg-white shadow-[0_16px_38px_rgba(31,37,68,0.04)]">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-[#e7ebff] text-left text-sm text-[#5e647f]">
            @if (!empty($headers))
                <thead class="bg-[#f7f9ff]">
                    <tr>
                        @foreach ($headers as $header)
                            <th scope="col" class="px-4 py-3 font-semibold text-[#1f2544]">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
            @endif

            <tbody class="divide-y divide-[#eef3ff] bg-white">
                @if (!empty($rows))
                    @foreach ($rows as $row)
                        <tr class="transition hover:bg-[#f7f9ff]">
                            @foreach ($row as $cell)
                                <td class="px-4 py-3 align-middle text-[#1f2544]">{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="{{ count($headers) ?: 1 }}" class="px-4 py-10 text-center text-[#5e647f]">
                            {{ $emptyMessage }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
