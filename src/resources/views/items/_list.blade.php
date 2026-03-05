@foreach ($items as $item)
<div class="item-card">
    <a href="{{ route('items.show', ['item_id' => $item->id]) }}" class="item-link">
        <div class="item-image-wrapper">
            <img src="{{ asset('storage/' . $item->item_image) }}"  alt="商品画像" class="item-image">
                @if($item->status == 1)
                    <div class="sold-label sold-label--sm">Sold</div>
                @endif
            <div class="item-name">
                {{ $item->name }}
            </div>
        </div>
    </a>
</div>
@endforeach