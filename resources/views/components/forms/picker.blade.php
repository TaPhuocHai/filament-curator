@php
    $statePath = $getStatePath();
    $items = $getState() ?? [];
    $itemsCount = count($items);
    $isMultiple = $isMultiple();
    $maxItems = $getMaxItems();
    $shouldDisplayAsList = $shouldDisplayAsList();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">

    <div
        x-data="{
            insertMedia: function (event) {
                if (event.detail.statePath !== '{{ $statePath }}') return;
                $wire.$set(event.detail.statePath, event.detail.media);
            },
        }"
        x-on:insert-content.window="insertMedia($event)"
        class="curator-media-picker w-full"
    >
        <ul
            @class([
                'w-full',
                'flex items-center gap-6 flex-wrap' => $itemsCount <= 3 && ! $shouldDisplayAsList,
                'curator-grid-container' => $itemsCount >= 3 && ! $shouldDisplayAsList,
                'overflow-hidden bg-white border border-gray-300 rounded-lg shadow-sm divide-y divide-gray-300 dark:border-gray-700 dark:text-white dark:divide-gray-700 dark:bg-white/5' => $itemsCount > 0 && $shouldDisplayAsList,
            ])
            x-sortable
            wire:end.stop="mountFormComponentAction('{{ $statePath }}', 'reorder', { items: $event.target.sortable.toArray() })"
            style="{{ $itemsCount === 1 ? '--grid-column-count: 1' : '' }}"
        >
            @foreach ($items as $uuid => $item)
                <li
                    wire:key="{{ $this->getId() }}.{{ $uuid }}.{{ $field::class }}.item"
                    x-sortable-item="{{ $uuid }}"
                    {{ $attributes->merge($getExtraAttributes())->class([
                        'relative w-full',
                    ]) }}
                >
                    @if ($shouldDisplayAsList)
                        <div class="w-full flex items-center gap-4 text-xs pe-2">
                            <div class="curator-picker-list-preview flex-shrink-0 h-12 w-12 checkered">
                                <x-curator::display
                                    :item="$item"
                                    :src="curator()->getThumbnailUrl($item->path)"
                                    :lazy="true"
                                    icon-classes="size-24"
                                />
                            </div>
                            <div class="curator-picker-list-details min-w-0 overflow-hidden py-2">
                                <p>{{ $item->pretty_name }}</p>
                            </div>
                            <div class="curator-picker-list-details flex-shrink-0 ml-auto py-2">
                                <p>{{ curator()->sizeForHumans($item->size) }}</p>
                            </div>
                            <div class="curator-picker-list-actions flex-shrink-0">
                                <div class="relative flex items-center">
                                    @if ($isMultiple)
                                        <div
                                            x-sortable-handle
                                            class="flex items-center justify-center flex-none w-8 h-8 transition text-gray-400 hover:text-gray-300"
                                        >
                                            {{ $getAction('reorder') }}
                                        </div>
                                    @endif

                                    <div class="flex items-center justify-center flex-none w-8 h-8">
                                        <x-filament-actions::group
                                            :actions="[
                                                $getAction('view')(['url' => $item->url]),
                                                $getAction('edit')(['id' => $item->id]),
                                                $getAction('download')(['uuid' => $uuid]),
                                                $getAction('remove')(['uuid' => $uuid]),
                                            ]"
                                            color="gray"
                                            size="xs"
                                            dropdown-placement="bottom-end"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div
                            @class([
                                'relative block w-full overflow-hidden border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white flex justify-center checkered',
                                'h-64' => ! curator()->isVideo($item->ext),
                                'md:flex-1 ' => $itemsCount <= 3,
                            ])
                        >
                            <x-curator::display
                                :item="$item"
                                :src="curator()->getMediumUrl($item->path)"
                                :lazy="true"
                                icon-classes="size-24"
                            />

                            <div class="absolute top-0 right-0">
                                <div class="relative flex items-center bg-gray-950 divide-x divide-gray-700 rounded-bl-lg shadow-md">
                                    @if ($isMultiple)
                                        <div
                                            x-sortable-handle
                                            class="flex items-center justify-center flex-none w-10 h-10 transition text-gray-400 hover:text-gray-300"
                                        >
                                            {{ $getAction('reorder') }}
                                        </div>
                                    @endif

                                    <div class="flex items-center justify-center flex-none w-10 h-10">
                                        <x-filament-actions::group
                                            :actions="[
                                                $getAction('view')(['url' => $item->url]),
                                                $getAction('edit')(['id' => $item->id]),
                                                $getAction('download')(['uuid' => $uuid]),
                                                $getAction('remove')(['uuid' => $uuid]),
                                            ]"
                                            color="gray"
                                            size="xs"
                                            dropdown-placement="bottom-end"
                                        />
                                    </div>
                                </div>
                            </div>

                            @if (! curator()->isVideo($item->ext))
                                <x-curator::display.info-overlay :label="$item->pretty_name" :size="$item->size" />
                            @endif
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>

        <div
            @class([
                'flex items-center gap-4',
                'mt-4' => $itemsCount > 0
            ])
        >
            @if ($itemsCount === 0 || $isMultiple)
                @if (! $maxItems || $itemsCount < $maxItems)
                    {{ $getAction('open_curator_picker') }}
                @endif
            @endif
            @if ($itemsCount > 1)
                {{ $getAction('removeAll') }}
            @endif
        </div>
    </div>
</x-dynamic-component>