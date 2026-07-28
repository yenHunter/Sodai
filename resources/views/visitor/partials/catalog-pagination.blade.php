@if ($products->hasPages())
    <div class="ec-pro-pagination">
        <span>Showing {{ $products->firstItem() }}-{{ $products->lastItem() }} of {{ $products->total() }} item(s)</span>
        <ul class="ec-pro-pagination-inner">
            @for ($page = 1; $page <= $products->lastPage(); $page++)
                <li>
                    <a class="{{ $page === $products->currentPage() ? 'active' : '' }}"
                        href="{{ $products->url($page) }}">{{ $page }}</a>
                </li>
            @endfor
            @if ($products->hasMorePages())
                <li><a class="next" href="{{ $products->nextPageUrl() }}">Next <i class="ecicon eci-angle-right"></i></a></li>
            @endif
        </ul>
    </div>
@endif