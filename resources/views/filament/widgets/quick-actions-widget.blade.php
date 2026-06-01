<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Quick Actions
        </x-slot>

        <x-slot name="description">
            Fast shortcuts for common IT operations.
        </x-slot>

        <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px;">
            @foreach ($actions as $action)
                <a
                    href="{{ $action['url'] }}"
                    style="
                        display: flex;
                        align-items: center;
                        gap: 14px;
                        padding: 16px;
                        border-radius: 18px;
                        background: rgba(15, 23, 42, 0.72);
                        border: 1px solid rgba(148, 163, 184, 0.18);
                        text-decoration: none;
                        box-shadow: 0 12px 30px rgba(0,0,0,0.18);
                        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
                    "
                    onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 18px 38px rgba(0,0,0,0.28)'; this.style.borderColor='{{ $action['color'] }}';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 12px 30px rgba(0,0,0,0.18)'; this.style.borderColor='rgba(148, 163, 184, 0.18)';"
                >
                    <span
                        style="
                            width: 44px;
                            height: 44px;
                            min-width: 44px;
                            max-width: 44px;
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            border-radius: 14px;
                            background: {{ $action['color'] }};
                            font-size: 20px;
                            line-height: 1;
                            overflow: hidden;
                        "
                    >
                        {{ $action['emoji'] }}
                    </span>

                    <span style="display: block; min-width: 0; flex: 1;">
                        <span style="display: block; color: #ffffff; font-weight: 700; font-size: 14px;">
                            {{ $action['label'] }}
                        </span>

                        <span style="display: block; color: #94a3b8; font-size: 12px; margin-top: 3px;">
                            {{ $action['description'] }}
                        </span>
                    </span>

                    <span style="color: {{ $action['color'] }}; font-size: 20px; font-weight: 700;">
                        ›
                    </span>
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>