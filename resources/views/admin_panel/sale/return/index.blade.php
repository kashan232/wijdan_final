@extends('admin_panel.layout.app')
@section('content')

<div class="container-fluid">
    <div class="card shadow-sm border-0 mt-3">
        <div class="card-header bg-light text-dark d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Sale Returns</h5>
            <a href="{{ url()->previous() }}" class="btn btn-danger btn-sm text-center">
                Back
            </a>
        </div>

        <div class="card-body">
            <!-- Filter Row -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">From Date</label>
                    <input type="date" id="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">To Date</label>
                    <input type="date" id="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" id="btnFilter" class="btn btn-primary w-100 shadow-sm border-0">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <button type="button" id="btnReset" class="btn btn-secondary ms-2 shadow-sm border-0">
                        Reset
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="returnsTable" class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th>#</th>
                            <th>Inv</th>
                            <th>Items</th>
                            <th>Customer</th>
                            <th>Total Items</th>
                            <th>Total Net</th>
                            <th>Return Note</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<style>
    /* Make search input wider */
    .dataTables_filter input {
        width: 300px !important;
        font-size: 1.1em;
        padding: 5px 10px;
        margin-bottom: 10px;
    }
</style>
<script>
    $(document).ready(function() {
        var table = $('#returnsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('sale.returns.index') }}",
                data: function (d) {
                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                }
            },
            columns: [
                { data: 0, orderable: false, searchable: false }, // S.No
                { data: 1 }, // Inv
                { data: 2, orderable: false, searchable: true }, // Items (Names are searched)
                { data: 3 }, // Customer
                { data: 4 }, // Total Items
                { data: 5 }, // Total Net
                { data: 6 }, // Return Note
                { data: 7 }, // Date
                { data: 8, orderable: false, searchable: false }, // Status
                { data: 9, orderable: false, searchable: false } // Action
            ],
            responsive: true,
            pageLength: 25,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            order: [
                [7, 'desc'] // Order by Date
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search Returns...",
            }
        });

        $('#btnFilter').on('click', function() {
            table.draw();
        });

        $('#btnReset').on('click', function() {
            $('#from_date').val('');
            $('#to_date').val('');
            table.draw();
        });
    });
</script>
@endsection