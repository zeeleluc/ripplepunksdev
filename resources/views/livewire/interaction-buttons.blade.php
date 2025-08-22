@php
    $emojis = [
        'thumb-up' => '👍🏼',
        'thumb-down' => '👎🏼',
        'middle-finger' => '🖕🏼',
        'eyes' => '👀',
        'lightning' => '⚡️',
        'heart' => '💙',
    ];

    // Merge default classes with passed $class property
    $wrapperClass = 'flex space-x-2';
    if (!empty($class)) {
        $wrapperClass .= ' ' . $class;
    }
@endphp

<div class="{{ $wrapperClass }}">
    @foreach($emojis as $type => $emoji)
        @php
            $count = $interactions[$type]['count'] ?? 0;
            $pressed = $interactions[$type]['pressed_by_user'] ?? false;
        @endphp
        <button
            wire:click="toggle('{{ $type }}')"
            {{ $canInteract ? '' : 'disabled' }}
            class="flex items-center space-x-1 px-2 py-1 rounded border
                {{ $canInteract
                    ? ($pressed
                        ? 'border-primary-500 hover:bg-primary-100'
                        : 'border-gray-300 hover:bg-gray-100')
                    : 'border-gray-200 bg-gray-50 cursor-not-allowed' }}"
        >
            <span class="text-base {{ $pressed ? 'text-current' : 'text-gray-400' }}">{{ $emoji }}</span>
            <span class="text-sm {{ $pressed ? 'text-primary-500 font-bold' : 'text-gray-500' }}">{{ $count }}</span>
        </button>
    @endforeach
</div>
