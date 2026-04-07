@php
    $record = $getRecord();
    
    if ($record->trigger_type !== 'manual') {
        return;
    }

    $percentage = $record->total_recipients > 0 
        ? round(($record->sent_count / $record->total_recipients) * 100) 
        : 0;
    
    $color = match($record->status) {
        'sending' => 'primary',
        'completed' => 'success',
        'cancelled' => 'danger',
        'scheduled' => 'warning',
        default => 'gray'
    };
@endphp

<div class="flex flex-col w-full min-w-[120px] gap-1 px-4 py-2">
    <div class="flex items-center justify-between text-xs font-medium">
        <span class="text-gray-500 uppercase">{{ $record->status }}</span>
        <span class="text-{{ $color }}-600">{{ $percentage }}%</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-1.5 dark:bg-gray-700">
        <div class="bg-{{ $color === 'primary' ? 'blue' : ($color === 'success' ? 'green' : ($color === 'warning' ? 'yellow' : ($color === 'danger' ? 'red' : 'gray'))) }}-600 h-1.5 rounded-full transition-all duration-500" 
             style="width: {{ $percentage }}%"></div>
    </div>
</div>
