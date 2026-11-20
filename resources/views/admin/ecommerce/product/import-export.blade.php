@extends('admin.include.vertical', ['title' => 'Product Import / Export'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Ecommerce', 'title' => 'Product Import / Export'])

    <div class="row">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Export products</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Downloads every product and variant as a single CSV: one row per variant, with the
                        product columns repeated on each row. The file uses the same columns the importer
                        expects, so it can be edited in a spreadsheet and imported back.
                    </p>
                    <a class="btn btn-primary" href="{{ route('admin.ecommerce.product.export') }}">
                        <i class="fs-sm me-2" data-lucide="download"></i>
                        Export all products (CSV)
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Template</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-2">
                        Start from a sample file with the exact column headers and one example row.
                    </p>
                    <a class="btn btn-light" href="{{ route('admin.ecommerce.product.import-template') }}">
                        <i class="fs-sm me-2" data-lucide="file-spreadsheet"></i>
                        Download CSV template
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">How import matches records</h4>
                </div>
                <div class="card-body">
                    <ul class="mb-0 ps-3 text-muted">
                        <li>Rows with the same <code>product_name</code> belong to one product; product columns
                            come from the first of those rows.</li>
                        <li>A variant whose <code>variant_sku</code> already exists on that product is updated;
                            a blank SKU creates a new variant with a generated SKU.</li>
                        <li>A SKU belonging to another product is rejected, since SKUs are unique.</li>
                        <li>Categories must already exist (use an ID, a name, or <code>Parent &gt; Child</code>).
                            A brand name that does not exist yet is created.</li>
                        <li>If any row of a product is invalid, that whole product is skipped and nothing is
                            written for it. Other products in the file still import.</li>
                        <li>Options use <code>Option: Value</code> pairs separated by a pipe, for example
                            <code>Color: Red | Size: M</code>.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Import products</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.ecommerce.product.import') }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" for="csvFile">CSV file</label>
                            <input class="form-control @error('file') is-invalid @enderror" id="csvFile"
                                name="file" type="file" accept=".csv,text/csv" required />
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Up to 5 MB and 5,000 data rows per file.</div>
                        </div>

                        @admincan('product.create')
                            <button class="btn btn-primary" type="submit">
                                <i class="fs-sm me-2" data-lucide="upload"></i>
                                Import CSV
                            </button>
                        @else
                            <div class="alert alert-info mb-0">
                                Your role can view products but not create them, so importing is disabled.
                            </div>
                        @endadmincan
                    </form>
                </div>
            </div>

            @if (session('import_result'))
                @php($result = session('import_result'))
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Last import result</h4>
                        <span class="badge badge-soft-primary fs-xxs">
                            {{ $result['stats']['rows'] }} data row{{ $result['stats']['rows'] === 1 ? '' : 's' }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row text-center g-3 mb-3">
                            <div class="col-6 col-md-3">
                                <h3 class="mb-0">{{ $result['stats']['products_created'] }}</h3>
                                <small class="text-muted">Products created</small>
                            </div>
                            <div class="col-6 col-md-3">
                                <h3 class="mb-0">{{ $result['stats']['products_updated'] }}</h3>
                                <small class="text-muted">Products updated</small>
                            </div>
                            <div class="col-6 col-md-3">
                                <h3 class="mb-0">{{ $result['stats']['variants_created'] }}</h3>
                                <small class="text-muted">Variants added</small>
                            </div>
                            <div class="col-6 col-md-3">
                                <h3 class="mb-0">{{ $result['stats']['variants_updated'] }}</h3>
                                <small class="text-muted">Variants updated</small>
                            </div>
                        </div>

                        @if ($result['error_count'] > 0)
                            <h5 class="mb-2">
                                Skipped rows
                                @if ($result['error_count'] > count($result['errors']))
                                    <small class="text-muted">(showing the first {{ count($result['errors']) }}
                                        of {{ $result['error_count'] }})</small>
                                @endif
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 90px">Row</th>
                                            <th>Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($result['errors'] as $error)
                                            <tr>
                                                <td>{{ $error['row'] }}</td>
                                                <td>{{ $error['message'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-success mb-0">
                                <i class="me-2" data-lucide="circle-check"></i>
                                Every row was imported.
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Columns</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($columns as $column)
                            <code class="bg-light rounded px-2 py-1">{{ $column }}</code>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.include.partials.footer-scripts')
@endsection
