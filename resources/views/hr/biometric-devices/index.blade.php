@extends('admin_panel.layout.app')

@section('content')
    @include('hr.partials.hr-styles')
    <div class="container">
        <!-- Page Header -->
        <div class="page-header d-flex justify-content-between align-items-start">
            <div>
                <h1 class="page-title"><i class="fa fa-fingerprint"></i> Biometric Devices</h1>
                <p class="page-subtitle">Manage fingerprint attendance devices</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-light border" data-bs-toggle="modal" data-bs-target="#deviceGuideModal">
                    <i class="fa fa-question-circle text-primary me-1"></i> Setup Guide
                </button>
                @can('hr.biometric.devices.create')
                    <button type="button" class="btn btn-create" id="addDeviceBtn">
                        <i class="fa fa-plus"></i> Add Device
                    </button>
                @endcan
            </div>
        </div>

        <!-- Global Attendance Settings Card -->
        <div class="card mb-4" style="border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-left: 4px solid #667eea;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="mb-1"><i class="fa fa-cog text-primary me-2"></i>Global Attendance Settings</h5>
                        <small class="text-muted">Settings that apply to all employees</small>
                    </div>
                </div>
                <form id="globalSettingsForm" class="row align-items-end">
                    @csrf
                    <div class="col-md-4">
                        <label class="form-label"><i class="fa fa-stopwatch me-1"></i> Punch Gap (Minutes)</label>
                        <input type="number" name="attendance_punch_gap_minutes" id="punch_gap_minutes" class="form-control" value="{{ \App\Models\Hr\HrSetting::getPunchGapMinutes() }}" min="1" max="120" required>
                        <small class="text-muted">Min. gap between check-in and check-out punches</small>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary" id="saveSettingsBtn">
                            <i class="fa fa-save me-1"></i> Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Devices Grid -->
        <div class="row">
            @forelse ($devices as $device)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100" style="border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="mb-1">{{ $device->name }}</h5>
                                    <small class="text-muted">{{ $device->model ?? 'N/A' }}</small>
                                </div>
                                <span class="badge {{ $device->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $device->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fa fa-network-wired me-2 text-primary"></i>
                                    <span>{{ $device->ip_address }}:{{ $device->port }}</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fa fa-users me-2 text-success"></i>
                                    <span>{{ $device->employees->count() }} employees enrolled</span>
                                </div>
                                @if ($device->last_sync_at)
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-sync me-2 text-info"></i>
                                        <small>Last sync: {{ $device->last_sync_at->diffForHumans() }}</small>
                                    </div>
                                @endif
                            </div>

                            @if ($device->notes)
                                <p class="text-muted small mb-3">{{ $device->notes }}</p>
                            @endif

                            <div class="btn-group w-100 mb-2" role="group">
                                @can('hr.biometric.devices.edit')
                                    <button class="btn btn-sm btn-primary test-connection-btn" data-id="{{ $device->id }}">
                                        <i class="fa fa-plug"></i> Test
                                    </button>
                                    <button class="btn btn-sm btn-info sync-employees-btn" data-id="{{ $device->id }}">
                                        <i class="fa fa-users"></i> Sync
                                    </button>
                                @endcan
                            </div>

                            <div class="btn-group w-100" role="group">
                                @can('hr.biometric.devices.edit')
                                    <button class="btn btn-sm btn-outline-secondary edit-device-btn" 
                                        data-id="{{ $device->id }}" 
                                        data-name="{{ $device->name }}"
                                        data-ip="{{ $device->ip_address }}" 
                                        data-port="{{ $device->port }}"
                                        data-username="{{ $device->username }}" 
                                        data-model="{{ $device->model }}"
                                        data-notes="{{ $device->notes }}" 
                                        data-active="{{ $device->is_active }}">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                @endcan
                                @can('hr.biometric.devices.delete')
                                    <button class="btn btn-sm btn-outline-danger delete-device-btn" data-id="{{ $device->id }}">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 py-5 text-center">
                    <i class="fa fa-fingerprint fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No biometric devices configured yet.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Device Modal -->
    <div class="modal fade" id="deviceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header gradient">
                    <h5 class="modal-title">
                        <i class="fa fa-fingerprint"></i>
                        <span id="modalTitle">Add Device</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="deviceForm" action="{{ route('hr.biometric-devices.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="device_id" name="device_id">
                    <input type="hidden" id="_method" name="_method" value="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">Device Name</label>
                                <input type="text" name="name" id="device_name" class="form-control" required>
                            </div>
                            <div class="col-8 mb-3">
                                <label class="form-label">IP Address</label>
                                <input type="text" name="ip_address" id="device_ip" class="form-control" required>
                            </div>
                            <div class="col-4 mb-3">
                                <label class="form-label">Port</label>
                                <input type="number" name="port" id="device_port" class="form-control" value="4370" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Model</label>
                                <input type="text" name="model" id="device_model" class="form-control">
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" id="device_active" class="form-check-input" value="1" checked>
                                    <label class="form-check-label" for="device_active">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Device</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Save Global Settings
            $('#globalSettingsForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: '{{ route('hr.settings.update') }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire('Saved!', response.success, 'success');
                    }
                });
            });

            // Add Modal
            $('#addDeviceBtn').click(function() {
                $('#device_id').val('');
                $('#_method').val('POST');
                $('#deviceForm')[0].reset();
                $('#modalTitle').text('Add Device');
                $('#deviceModal').modal('show');
            });

            // Edit Modal
            $(document).on('click', '.edit-device-btn', function() {
                const data = $(this).data();
                $('#device_id').val(data.id);
                $('#_method').val('PUT');
                $('#device_name').val(data.name);
                $('#device_ip').val(data.ip);
                $('#device_port').val(data.port);
                $('#device_model').val(data.model);
                $('#device_active').prop('checked', data.active == 1);
                $('#modalTitle').text('Edit Device');
                $('#deviceModal').modal('show');
            });

            // Store/Update
            $('#deviceForm').submit(function(e) {
                e.preventDefault();
                const id = $('#device_id').val();
                const url = id ? '{{ url('hr/biometric-devices') }}/' + id : '{{ route('hr.biometric-devices.store') }}';
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire('Success!', response.success, 'success').then(() => location.reload());
                    }
                });
            });

            // Test Connection
            $(document).on('click', '.test-connection-btn', function() {
                const id = $(this).data('id');
                $.post('{{ url('hr/biometric-devices') }}/' + id + '/test', { _token: '{{ csrf_token() }}' }, function(response) {
                    if (response.success) Swal.fire('Connected!', response.success, 'success');
                    else Swal.fire('Failed', response.error, 'error');
                });
            });

            // Sync Employees
            $(document).on('click', '.sync-employees-btn', function() {
                const id = $(this).data('id');
                Swal.fire({ title: 'Syncing...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                $.post('{{ url('hr/biometric-devices') }}/' + id + '/sync-employees', { _token: '{{ csrf_token() }}' }, function(response) {
                    Swal.close();
                    if (response.success) Swal.fire('Synced!', response.success, 'success');
                    else Swal.fire('Error', response.error, 'error');
                });
            });

            // Delete Device
            $(document).on('click', '.delete-device-btn', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Delete Device?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, delete!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ url('hr/biometric-devices') }}/' + id,
                            type: 'DELETE',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function(response) {
                                Swal.fire('Deleted!', response.success, 'success').then(() => location.reload());
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
