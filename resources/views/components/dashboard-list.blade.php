@props(['stats', 'isMoney' => false])

@if(empty($stats))
    <p class="text-xs text-gray-400 mt-2">Sem dados no período.</p>
@else
    <div class="mt-3 space-y-2">
        @foreach($stats as $status => $val)
            <div class="flex justify-between items-center text-sm">
                <x-status-badge :status="$status" class="!text-[10px] px-2 py-0.5" />
                
                <span class="font-semibold text-gray-800">
                    {{ $isMoney ? 'R$ ' . number_format($val, 2, ',', '.') : $val }}
                </span>
            </div>
        @endforeach
    </div>
@endif