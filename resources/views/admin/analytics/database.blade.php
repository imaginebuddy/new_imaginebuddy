@extends('admin.layout')

@section('content')
<div class="content">
  @include('admin.analytics.nav')

  @if(session('success_message'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center mb-4" role="alert">
      <i class="bi bi-check-circle-fill fs-5 me-2"></i>
      <div>{{ session('success_message') }}</div>
      <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if(session('error_message'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 d-flex align-items-center mb-4" role="alert">
      <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
      <div>{{ session('error_message') }}</div>
      <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <!-- Top Overview Card -->
  <div class="card shadow-custom border-0 mb-4 bg-dark text-white">
    <div class="card-body p-4">
      <div class="d-flex flex-wrap justify-content-between align-items-center">
        <div>
          <div class="d-flex align-items-center mb-2">
            <i class="bi bi-database-gear text-primary fs-4 me-2"></i>
            <span class="text-uppercase tracking-wider small fw-bold text-primary">Storage &amp; Table Management</span>
          </div>
          <h2 class="display-6 fw-bold mb-1">
            {{ number_format($totalRows) }}
            <small class="fs-6 text-white-50 fw-normal">Total Records across 4 Analytics Tables</small>
          </h2>
          <small class="text-white-50">Export full table logs to CSV anytime, or safely empty tables to free server database storage.</small>
        </div>

        <div class="mt-3 mt-md-0 d-flex gap-2">
          <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-7 align-self-center">
            <i class="bi bi-shield-check me-1"></i> Isolated from Core App Tables
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- Tables Management Cards Grid -->
  <div class="row g-4">
    @foreach($tables as $key => $table)
      <div class="col-md-6 col-xl-6">
        <div class="card shadow-custom border-0 h-100">
          <div class="card-header bg-transparent border-0 pt-4 pb-0 d-flex justify-content-between align-items-start">
            <div>
              <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-{{ $table['color'] }}-subtle text-{{ $table['color'] }} border border-{{ $table['color'] }}-subtle p-2 rounded">
                  <i class="bi {{ $table['icon'] }} fs-5"></i>
                </span>
                <div>
                  <h5 class="card-title m-0 fw-bold">{{ $table['title'] }}</h5>
                  <code class="small text-muted">{{ $table['name'] }}</code>
                </div>
              </div>
            </div>
            <span class="badge bg-{{ $table['count'] > 0 ? 'dark' : 'light text-dark border' }} px-3 py-2 fs-7">
              {{ number_format($table['count']) }} records
            </span>
          </div>

          <div class="card-body">
            <p class="text-muted small mb-3">
              {{ $table['description'] }}
            </p>

            <div class="bg-light rounded p-3 mb-4 small">
              <div class="d-flex justify-content-between mb-1">
                <span class="text-muted"><i class="bi bi-calendar-event me-1"></i> First Recorded:</span>
                <span class="fw-medium text-dark">{{ $table['first_date'] }}</span>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-muted"><i class="bi bi-clock-history me-1"></i> Latest Recorded:</span>
                <span class="fw-medium text-dark">{{ $table['last_date'] }}</span>
              </div>
            </div>

            <div class="d-flex flex-wrap gap-2 pt-2 border-top">
              <!-- Download CSV Button -->
              <a href="{{ url('panel/admin/analytics/export/' . $key) }}" 
                 class="btn btn-success btn-sm d-inline-flex align-items-center flex-grow-1 justify-content-center @if($table['count'] == 0) disabled @endif">
                <i class="bi bi-download me-1"></i> Download CSV
              </a>

              <!-- Empty Table Button -->
              <button type="button" 
                      class="btn btn-outline-danger btn-sm d-inline-flex align-items-center flex-grow-1 justify-content-center @if($table['count'] == 0) disabled @endif"
                      onclick="openTruncateModal('{{ $key }}', '{{ $table['title'] }}', {{ $table['count'] }})">
                <i class="bi bi-trash3 me-1"></i> Empty Table
              </button>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>

<!-- Modal for Empty Table Confirmation -->
<div class="modal fade" id="truncateConfirmModal" tabindex="-1" aria-labelledby="truncateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-danger text-white border-0">
        <h5 class="modal-title" id="truncateModalLabel">
          <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Empty Table
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" id="truncateForm" action="">
        @csrf
        <div class="modal-body p-4">
          <p class="mb-2">Are you sure you want to permanently empty table <code class="fw-bold fs-6" id="modalTableName"></code>?</p>
          <div class="alert alert-warning border-0 small mb-3">
            <i class="bi bi-info-circle-fill me-1"></i> This action will delete all <strong id="modalRowCount"></strong> records from this table. This action <strong>cannot be undone</strong>.
          </div>
          <p class="small text-muted mb-0">Tip: You can download a complete CSV backup before emptying the table.</p>
        </div>
        <div class="modal-footer border-0 bg-light p-3">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <a href="#" id="modalDownloadBackupBtn" class="btn btn-outline-success btn-sm">
            <i class="bi bi-download me-1"></i> Download CSV First
          </a>
          <button type="submit" class="btn btn-danger btn-sm">
            <i class="bi bi-trash3 me-1"></i> Yes, Empty Table
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('javascript')
<script>
function openTruncateModal(tableName, tableTitle, rowCount) {
  if (rowCount === 0) return;

  document.getElementById('modalTableName').textContent = tableName;
  document.getElementById('modalRowCount').textContent = rowCount.toLocaleString();
  
  var form = document.getElementById('truncateForm');
  form.action = '{{ url("panel/admin/analytics/truncate") }}/' + tableName;

  var backupBtn = document.getElementById('modalDownloadBackupBtn');
  backupBtn.href = '{{ url("panel/admin/analytics/export") }}/' + tableName;

  var modal = new bootstrap.Modal(document.getElementById('truncateConfirmModal'));
  modal.show();
}
</script>
@endsection
