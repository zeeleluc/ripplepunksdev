@php
    $emojis = [
        'thumb-up' => '👍🏼',
        'eyes' => '👀',
        'lightning' => '⚡️',
        'heart' => '💙',
        'eggplant' => '🍆',
        'thumb-down' => '👎🏼',
        'middle-finger' => '🖕🏼',
    ];

    $wrapperClass = 'inline-flex rounded-md overflow-hidden border border-gray-300';
    if (!empty($class)) {
        $wrapperClass .= ' ' . $class;
    }
@endphp

<div class="{{ $wrapperClass }}">
    @foreach($interactions as $type => $data)
        @php
            $emoji = $emojis[$type] ?? '';
            $count = $data['count'];
            $pressed = $data['pressed_by_user'];
        @endphp
        <button
            wire:click="toggle('{{ $type }}')"
            {{ $canInteract ? '' : 'disabled' }}
            class="flex items-center gap-1 px-1.5 py-1 text-xs
                {{ $canInteract
                    ? ($pressed
                        ? 'bg-primary-50 text-primary-600'
                        : 'bg-white hover:bg-gray-100 text-gray-600')
                    : 'bg-gray-50 text-gray-400 cursor-not-allowed' }}"
        >
            <span>{{ $emoji }}</span>
            <span class="text-xs {{ $pressed ? 'font-bold text-primary-600' : '' }}">{{ $count }}</span>
        </button>
    @endforeach
</div>
