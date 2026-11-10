@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-xs text-muted">
            Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} data
        </p>
        <div class="flex flex-wrap items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="pagination-button is-disabled" aria-disabled="true">Sebelumnya</span>
            @else
                <button type="button" class="pagination-button" wire:click="previousPage('{{ $paginator->getPageName() }}')" rel="prev">Sebelumnya</button>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="pagination-ellipsis">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page === $paginator->currentPage())
                            <span class="pagination-button is-current" aria-current="page">{{ $page }}</span>
                        @else
                            <button type="button" class="pagination-button pagination-page" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')">{{ $page }}</button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <button type="button" class="pagination-button" wire:click="nextPage('{{ $paginator->getPageName() }}')" rel="next">Berikutnya</button>
            @else
                <span class="pagination-button is-disabled" aria-disabled="true">Berikutnya</span>
            @endif
        </div>
    </nav>
@endif
