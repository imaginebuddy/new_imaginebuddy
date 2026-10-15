@extends('admin.layout')

@section('content')
<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <span class="text-muted">Hire Project Inquiries</span>
</h5>

<div class="content">
    @if (session('success_message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check2 me-1"></i> {{ session('success_message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Metric Counters -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-4 col-xl-2">
            <div class="card shadow-custom border-0 p-3 text-center">
                <span class="text-muted small fw-bold text-uppercase">Total Inquiries</span>
                <h3 class="fw-bold mb-0 text-dark">{{ $counts['total'] }}</h3>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-xl-2">
            <div class="card shadow-custom border-0 p-3 text-center border-start border-primary border-4">
                <span class="text-muted small fw-bold text-uppercase">New Leads</span>
                <h3 class="fw-bold mb-0 text-primary">{{ $counts['new'] }}</h3>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-xl-2">
            <div class="card shadow-custom border-0 p-3 text-center border-start border-info border-4">
                <span class="text-muted small fw-bold text-uppercase">In Review</span>
                <h3 class="fw-bold mb-0 text-info">{{ $counts['in_review'] }}</h3>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-xl-2">
            <div class="card shadow-custom border-0 p-3 text-center border-start border-warning border-4">
                <span class="text-muted small fw-bold text-uppercase">Contacted</span>
                <h3 class="fw-bold mb-0 text-warning">{{ $counts['contacted'] }}</h3>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-xl-2">
            <div class="card shadow-custom border-0 p-3 text-center border-start border-purple border-4" style="border-color: #6f42c1 !important;">
                <span class="text-muted small fw-bold text-uppercase">Quoted</span>
                <h3 class="fw-bold mb-0" style="color: #6f42c1;">{{ $counts['quoted'] }}</h3>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-xl-2">
            <div class="card shadow-custom border-0 p-3 text-center border-start border-success border-4">
                <span class="text-muted small fw-bold text-uppercase">Converted</span>
                <h3 class="fw-bold mb-0 text-success">{{ $counts['converted'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Main Inquiries Table Card -->
    <div class="card shadow-custom border-0">
        <div class="card-header bg-white border-bottom py-3">
            <div class="row align-items-center g-2">
                <div class="col-md-4">
                    <h6 class="m-0 fw-bold text-dark"><i class="bi bi-inbox me-2"></i> Client Inquiries & Creative Briefs</h6>
                </div>
                <div class="col-md-8 text-md-end">
                    <form method="GET" action="{{ url('panel/admin/hire/inquiries') }}" class="d-inline-flex flex-wrap gap-2 align-items-center">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search name, company, email..." style="width: 200px;">
                        
                        <select name="status" class="form-select form-select-sm" style="width: 130px;" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="new" @selected(request('status') == 'new')>New</option>
                            <option value="in_review" @selected(request('status') == 'in_review')>In Review</option>
                            <option value="contacted" @selected(request('status') == 'contacted')>Contacted</option>
                            <option value="quoted" @selected(request('status') == 'quoted')>Quoted</option>
                            <option value="converted" @selected(request('status') == 'converted')>Converted</option>
                            <option value="archived" @selected(request('status') == 'archived')>Archived</option>
                        </select>

                        <select name="currency" class="form-select form-select-sm" style="width: 100px;" onchange="this.form.submit()">
                            <option value="">All Currencies</option>
                            <option value="USD" @selected(request('currency') == 'USD')>USD ($)</option>
                            <option value="INR" @selected(request('currency') == 'INR')>INR (₹)</option>
                        </select>

                        <button type="submit" class="btn btn-sm btn-dark"><i class="bi bi-search"></i></button>
                        
                        @if(request()->anyFilled(['q', 'status', 'currency']))
                        <a href="{{ url('panel/admin/hire/inquiries') }}" class="btn btn-sm btn-outline-secondary" title="Clear Filters"><i class="bi bi-x-circle"></i></a>
                        @endif

                        <a href="{{ url('panel/admin/hire/inquiries/export') }}?status={{ request('status') }}" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
                        </a>
                        <a href="{{ url('panel/admin/hire/settings') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-gear me-1"></i> Settings
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Client / Company</th>
                            <th>Contact</th>
                            <th>Category & SKUs</th>
                            <th>Budget</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inquiries as $inquiry)
                        <tr>
                            <td class="ps-4 fw-bold text-muted">#{{ $inquiry->id }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $inquiry->company_name }}</div>
                                <div class="small text-muted">{{ $inquiry->full_name }}</div>
                                @if($inquiry->website_url)
                                <a href="{{ $inquiry->website_url }}" target="_blank" class="small text-primary text-decoration-none">
                                    <i class="bi bi-link-45deg"></i> Website
                                </a>
                                @endif
                            </td>
                            <td>
                                <div><a href="mailto:{{ $inquiry->email }}" class="text-reset text-decoration-underline">{{ $inquiry->email }}</a></div>
                                <span class="badge bg-light text-muted border">{{ $inquiry->country_code ?: 'N/A' }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $inquiry->product_category }}</div>
                                <div class="small text-muted">{{ $inquiry->sku_count }}</div>
                            </td>
                            <td>
                                @if($inquiry->budget_range)
                                <span class="badge bg-light text-dark border">
                                    {{ $inquiry->currency == 'INR' ? '₹' : '$' }} {{ $inquiry->budget_range }}
                                </span>
                                @else
                                <span class="text-muted small">Not specified</span>
                                @endif
                            </td>
                            <td>
                                {!! $inquiry->status_badge !!}
                            </td>
                            <td class="small text-muted">
                                <div>{{ $inquiry->created_at->format('M d, Y') }}</div>
                                <div class="f-size-11">{{ $inquiry->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="pe-4 text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary me-1 view-brief-btn" data-inquiry='@json($inquiry)'>
                                    <i class="bi bi-eye"></i> View
                                </button>
                                
                                <form method="POST" action="{{ url('panel/admin/hire/inquiries/' . $inquiry->id . '/delete') }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this inquiry?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                                No custom photoshoot inquiries found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($inquiries->hasPages())
            <div class="p-3 border-top d-flex justify-content-end">
                {{ $inquiries->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal: View Inquiry Brief & Update Status -->
<div class="modal fade" id="inquiryDetailModal" tabindex="-1" aria-labelledby="inquiryModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h6 class="modal-title" id="inquiryModalTitle"><i class="bi bi-briefcase me-2"></i> Project Inquiry Details</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold text-uppercase d-block">Client Name</label>
                        <span id="modalFullName" class="fw-bold fs-6"></span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold text-uppercase d-block">Company / Brand</label>
                        <span id="modalCompanyName" class="fw-bold fs-6"></span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold text-uppercase d-block">Work Email</label>
                        <span id="modalEmail"></span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold text-uppercase d-block">Website / Store</label>
                        <span id="modalWebsite"></span>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small fw-bold text-uppercase d-block">Product Category</label>
                        <span id="modalCategory" class="badge bg-secondary"></span>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small fw-bold text-uppercase d-block">SKU Count</label>
                        <span id="modalSkuCount"></span>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small fw-bold text-uppercase d-block">Budget Range</label>
                        <span id="modalBudget" class="fw-bold text-success"></span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold text-uppercase d-block">Photoshoot Types</label>
                        <div id="modalShootTypes" class="d-flex flex-wrap gap-1 mt-1"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold text-uppercase d-block">Intended Uses</label>
                        <div id="modalIntendedUses" class="d-flex flex-wrap gap-1 mt-1"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold text-uppercase d-block">Preferred Direction</label>
                        <span id="modalPreferredDirection"></span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold text-uppercase d-block">Desired Delivery Date</label>
                        <span id="modalDeliveryDate"></span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Creative Brief & Description</label>
                    <div id="modalDescription" class="p-3 bg-light rounded border" style="white-space: pre-wrap;"></div>
                </div>

                <div class="mb-4" id="modalRefContainer">
                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Reference Links</label>
                    <div id="modalReferenceLinks" class="p-2 bg-light rounded border text-break"></div>
                </div>

                <div class="mb-4" id="modalNotesContainer">
                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Client Additional Notes</label>
                    <div id="modalAdditionalNotes" class="p-2 bg-light rounded border" style="white-space: pre-wrap;"></div>
                </div>

                <hr>

                <!-- Update Status Form -->
                <form id="updateStatusForm" method="POST" action="">
                    @csrf
                    <h6 class="fw-bold mb-3"><i class="bi bi-pencil-square me-1"></i> Update Lead Status & Internal Notes</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Lead Status</label>
                            <select name="status" id="modalStatusSelect" class="form-select" required>
                                <option value="new">New</option>
                                <option value="in_review">In Review</option>
                                <option value="contacted">Contacted</option>
                                <option value="quoted">Quoted</option>
                                <option value="converted">Converted</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Internal Admin Notes</label>
                            <textarea name="admin_notes" id="modalAdminNotes" rows="2" class="form-control" placeholder="Add follow-up notes, quote details, communication summary..."></textarea>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Save Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const detailModal = new bootstrap.Modal(document.getElementById('inquiryDetailModal'));
    
    document.querySelectorAll('.view-brief-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const data = JSON.parse(this.getAttribute('data-inquiry'));
            
            document.getElementById('modalFullName').textContent = data.full_name || 'N/A';
            document.getElementById('modalCompanyName').textContent = data.company_name || 'N/A';
            document.getElementById('modalEmail').innerHTML = `<a href="mailto:${data.email}">${data.email}</a>`;
            document.getElementById('modalWebsite').innerHTML = data.website_url ? `<a href="${data.website_url}" target="_blank">${data.website_url}</a>` : '<span class="text-muted">None</span>';
            document.getElementById('modalCategory').textContent = data.product_category || 'N/A';
            document.getElementById('modalSkuCount').textContent = data.sku_count || 'N/A';
            document.getElementById('modalBudget').textContent = data.budget_range ? `${data.currency == 'INR' ? '₹' : '$'} ${data.budget_range} (${data.currency})` : 'Not specified';
            
            // Shoot types badges
            const shootContainer = document.getElementById('modalShootTypes');
            shootContainer.innerHTML = '';
            if (Array.isArray(data.photoshoot_types)) {
                data.photoshoot_types.forEach(t => {
                    const span = document.createElement('span');
                    span.className = 'badge bg-primary-subtle text-primary border border-primary-subtle';
                    span.textContent = t;
                    shootContainer.appendChild(span);
                });
            } else if (data.photoshoot_types) {
                shootContainer.textContent = data.photoshoot_types;
            }

            // Intended uses badges
            const usesContainer = document.getElementById('modalIntendedUses');
            usesContainer.innerHTML = '';
            if (Array.isArray(data.intended_use)) {
                data.intended_use.forEach(u => {
                    const span = document.createElement('span');
                    span.className = 'badge bg-light text-dark border';
                    span.textContent = u;
                    usesContainer.appendChild(span);
                });
            } else if (data.intended_use) {
                usesContainer.textContent = data.intended_use;
            } else {
                usesContainer.innerHTML = '<span class="text-muted small">None</span>';
            }

            document.getElementById('modalPreferredDirection').textContent = data.preferred_direction || 'None specified';
            document.getElementById('modalDeliveryDate').textContent = data.desired_delivery_date ? data.desired_delivery_date.split('T')[0] : 'Flexible';
            document.getElementById('modalDescription').textContent = data.project_description || 'No description provided.';
            
            // Reference links
            const refContainer = document.getElementById('modalRefContainer');
            if (data.reference_links) {
                refContainer.style.display = 'block';
                document.getElementById('modalReferenceLinks').textContent = data.reference_links;
            } else {
                refContainer.style.display = 'none';
            }

            // Additional notes
            const notesContainer = document.getElementById('modalNotesContainer');
            if (data.additional_notes) {
                notesContainer.style.display = 'block';
                document.getElementById('modalAdditionalNotes').textContent = data.additional_notes;
            } else {
                notesContainer.style.display = 'none';
            }

            // Status form
            document.getElementById('modalStatusSelect').value = data.status || 'new';
            document.getElementById('modalAdminNotes').value = data.admin_notes || '';
            document.getElementById('updateStatusForm').action = `{{ url('panel/admin/hire/inquiries') }}/${data.id}/status`;

            detailModal.show();
        });
    });
});
</script>
@endsection
