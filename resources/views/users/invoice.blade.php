<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="_token" content="{!! csrf_token() !!}"/>
    <title>{{ trans('misc.invoice') }} #{{ str_pad($data->id, 5, "0", STR_PAD_LEFT) }} — {{ $settings->title }}</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    @include('includes.css_general')

    <link rel="shortcut icon" href="{{ url('public/img', $settings->favicon) }}" />
    <style>
      :root {
        --inv-primary: #111827;
        --inv-secondary: #4b5563;
        --inv-border: #e5e7eb;
        --inv-bg-soft: #f9fafb;
        --inv-emerald: #059669;
        --inv-emerald-light: #ecfdf5;
      }

      body {
        background-color: #f3f4f6;
        color: var(--inv-primary);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
      }

      /* Top Action Bar */
      .invoice-top-bar {
        max-width: 880px;
        margin: 24px auto 16px auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
      }

      .btn-invoice-action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.15s ease-in-out;
      }

      /* Main Document Paper */
      .invoice-paper {
        max-width: 880px;
        margin: 0 auto 40px auto;
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid var(--inv-border);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
        padding: 44px;
        position: relative;
      }

      /* Header & Branding */
      .brand-logo {
        max-height: 48px;
        width: auto;
        object-fit: contain;
      }

      .invoice-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
      }

      .badge-status-paid {
        background-color: var(--inv-emerald-light);
        color: var(--inv-emerald);
        border: 1px solid rgba(5, 150, 105, 0.2);
      }

      .badge-status-paid::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: var(--inv-emerald);
      }

      /* Entity Info Cards */
      .entity-card {
        background-color: var(--inv-bg-soft);
        border: 1px solid var(--inv-border);
        border-radius: 10px;
        padding: 20px;
        height: 100%;
      }

      .entity-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.075em;
        color: #9ca3af;
        margin-bottom: 8px;
      }

      .gstin-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        padding: 3px 8px;
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-size: 0.8rem;
        font-weight: 600;
        color: #1f2937;
        margin-top: 8px;
        cursor: pointer;
        user-select: all;
      }

      .gstin-pill:hover {
        background: #f9fafb;
        border-color: #9ca3af;
      }

      /* Particulars Strip */
      .particulars-strip {
        background: #ffffff;
        border: 1px solid var(--inv-border);
        border-radius: 8px;
        padding: 14px 20px;
        margin: 24px 0;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
      }

      .particular-item .p-label {
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6b7280;
        letter-spacing: 0.04em;
        margin-bottom: 3px;
      }

      .particular-item .p-val {
        font-size: 0.875rem;
        font-weight: 600;
        color: #111827;
      }

      /* Table Styling */
      .invoice-table {
        width: 100%;
        margin-bottom: 24px;
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid var(--inv-border);
        border-radius: 8px;
        overflow: hidden;
      }

      .invoice-table thead th {
        background-color: var(--inv-bg-soft);
        color: #4b5563;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 12px 16px;
        border-bottom: 1px solid var(--inv-border);
      }

      .invoice-table tbody td {
        padding: 16px;
        font-size: 0.875rem;
        color: #1f2937;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
      }

      .invoice-table tbody tr:last-child td {
        border-bottom: none;
      }

      /* Summary Box */
      .calculation-box {
        background: var(--inv-bg-soft);
        border: 1px solid var(--inv-border);
        border-radius: 10px;
        padding: 20px;
      }

      .calc-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        font-size: 0.875rem;
        color: #4b5563;
      }

      .calc-row.total-row {
        border-top: 1px solid #d1d5db;
        padding-top: 12px;
        margin-top: 8px;
        font-size: 1.15rem;
        font-weight: 700;
        color: #111827;
      }

      /* Export Statutory Banner */
      .export-lut-card {
        background: #f5f3ff;
        border: 1px solid #ddd6fe;
        border-left: 4px solid #7c3aed;
        border-radius: 6px;
        padding: 14px 18px;
        font-size: 0.825rem;
        color: #4c1d95;
        line-height: 1.5;
      }

      /* Toast notification */
      #copyToast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #111827;
        color: #ffffff;
        padding: 10px 18px;
        border-radius: 8px;
        font-size: 0.85rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        display: none;
        z-index: 9999;
      }

      /* Print Optimization */
      @media print {
        body {
          background: #ffffff !important;
          color: #000000 !important;
          padding: 0 !important;
        }

        .no-print, .invoice-top-bar, #copyToast {
          display: none !important;
        }

        .invoice-paper {
          box-shadow: none !important;
          border: none !important;
          max-width: 100% !important;
          width: 100% !important;
          padding: 0 !important;
          margin: 0 !important;
        }

        .entity-card, .particulars-strip, .invoice-table, .calculation-box {
          border-color: #d1d5db !important;
        }
      }
    </style>
  </head>

  <body>
    @php
      $invoiceCurrency = $data->currency ?: $settings->currency_code;
      $taxDetails = is_array($data->tax_details) ? $data->tax_details : (json_decode($data->tax_details, true) ?: []);
      $businessGstin = $taxDetails['business_gstin'] ?? $settings->gstin;
      $supplierState = $taxDetails['supplier_state'] ?? $settings->supplier_state;
      $lutNumber = $taxDetails['lut_number'] ?? $settings->lut_number;
      $sacCode = $taxDetails['sac_code'] ?? '998439';
      $isExport = ($data->tax_type === 'export_zero_rated');

      // Customer state resolution
      $customerStateName = $taxDetails['customer_state'] ?? ($data->user()->state ?: null);
      $customerCountryName = isset($data->user()->country()->country_name) ? $data->user()->country()->country_name : ($taxDetails['customer_country'] ?? ($invoiceCurrency === 'INR' ? 'India' : 'International'));

      // Clean Place of Supply
      $placeOfSupply = $taxDetails['place_of_supply'] ?? ($customerStateName ? $customerStateName : ($supplierState ?: 'India'));

      // Clean Description
      $planName = '';
      $planInterval = '';
      if ($data->subscriptions_id && $data->subscription()) {
        $rawPlanName = $data->subscription()->plan->name ?? 'Subscription';
        // Strip duplicate "Plan" if already present in plan name
        $planName = preg_replace('/\s+plan\b/i', '', $rawPlanName) . ' Plan Subscription';
        $planInterval = ($data->subscription()->interval == 'month') ? 'Billed monthly' : 'Billed yearly';
      }
    @endphp

    <!-- Top Action / Navigation Bar (Hidden in Print) -->
    <div class="invoice-top-bar no-print">
      <div>
        <a href="{{ url('account/subscription') }}" class="btn btn-sm btn-outline-secondary btn-invoice-action">
          <i class="bi bi-arrow-left"></i>
          <span>Back to Subscriptions</span>
        </a>
      </div>

      <div class="d-flex align-items-center gap-2">
        <button type="button" onclick="copyInvoiceLink()" class="btn btn-sm btn-light border btn-invoice-action" title="Copy public invoice link">
          <i class="bi bi-link-45deg"></i>
          <span>Copy Link</span>
        </button>
        <button type="button" onclick="window.print()" class="btn btn-sm btn-dark btn-invoice-action">
          <i class="bi bi-printer"></i>
          <span>Print / Download PDF</span>
        </button>
      </div>
    </div>

    <!-- Main Invoice Document Canvas -->
    <div class="invoice-paper">
      
      <!-- Header Row: Brand & Invoice Meta -->
      <div class="row align-items-start mb-4">
        <div class="col-sm-6 mb-3 mb-sm-0">
          <img src="{{ url('public/img', $settings->logo) }}" class="brand-logo" alt="{{ $settings->title }}">
        </div>

        <div class="col-sm-6 text-sm-end">
          <div class="d-inline-flex flex-column align-items-sm-end">
            <div class="d-flex align-items-center gap-2 mb-2">
              <span class="invoice-hero-badge badge-status-paid">Paid</span>
              <h4 class="mb-0 text-uppercase fw-bold text-dark" style="letter-spacing: -0.02em;">
                @if ($isExport)
                  Export Invoice
                @elseif (!empty($businessGstin))
                  Tax Invoice
                @else
                  Invoice
                @endif
              </h4>
            </div>

            <div class="text-muted small">
              <span class="d-inline-block"><strong>Invoice #:</strong> <span class="font-monospace text-dark fw-bold">#{{ str_pad($data->id, 5, "0", STR_PAD_LEFT) }}</span></span>
              <span class="mx-2 text-muted">|</span>
              <span class="d-inline-block"><strong>Date:</strong> {{ Helper::formatDate($data->created_at) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Entity Details Row (Billed By vs Billed To) -->
      <div class="row g-3 mb-3">
        <!-- Billed By (Supplier) -->
        <div class="col-md-6">
          <div class="entity-card">
            <div class="entity-label">Billed By (Supplier)</div>
            <div class="fw-bold text-dark fs-6 mb-1">{{ $settings->company ?: $settings->title }}</div>

            <div class="text-secondary small line-height-sm">
              @if ($settings->address)
                <div>{{ $settings->address }}</div>
              @endif
              @if ($settings->city || $settings->zip)
                <div>{{ $settings->city }} {{ $settings->zip }}</div>
              @endif
              @if ($supplierState)
                <div>State: <span class="text-dark fw-medium">{{ $supplierState }}</span></div>
              @endif
              @if ($settings->country)
                <div>{{ $settings->country }}</div>
              @endif
              <div>Email: {{ $settings->email_admin }}</div>
            </div>

            @if (!empty($businessGstin))
              <div class="gstin-pill" title="Click to copy Business GSTIN" onclick="copyText('{{ $businessGstin }}')">
                <i class="bi bi-shield-check text-success"></i>
                <span>GSTIN: {{ $businessGstin }}</span>
                <i class="bi bi-clipboard text-muted ms-1" style="font-size: 0.75rem;"></i>
              </div>
            @elseif ($settings->vat)
              <div class="mt-2 text-muted small">VAT / Tax ID: <strong>{{ $settings->vat }}</strong></div>
            @endif
          </div>
        </div>

        <!-- Billed To (Customer) -->
        <div class="col-md-6">
          <div class="entity-card">
            <div class="entity-label">Billed To (Customer)</div>
            <div class="fw-bold text-dark fs-6 mb-1">
              {{ $data->user()->name }}
              @if ($data->user()->company)
                <span class="text-secondary fw-normal">({{ $data->user()->company }})</span>
              @endif
            </div>

            <div class="text-secondary small line-height-sm">
              @if ($data->user()->address)
                <div>{{ $data->user()->address }}</div>
              @endif
              @if ($data->user()->city || $data->user()->zip)
                <div>{{ $data->user()->city }} {{ $data->user()->zip }}</div>
              @endif
              @if ($customerStateName)
                <div>State: <span class="text-dark fw-medium">{{ $customerStateName }}</span></div>
              @endif
              <div>{{ $customerCountryName }}</div>
              <div>Email: {{ $data->user()->email }}</div>
            </div>

            @if (!empty($data->user()->customer_gstin))
              <div class="gstin-pill" title="Customer GSTIN (B2B Tax Credit)" onclick="copyText('{{ $data->user()->customer_gstin }}')">
                <i class="bi bi-briefcase text-primary"></i>
                <span>GSTIN: {{ $data->user()->customer_gstin }}</span>
              </div>
            @endif
          </div>
        </div>
      </div>

      <!-- Particulars Strip -->
      <div class="particulars-strip">
        <div class="particular-item">
          <div class="p-label">Place of Supply (POS)</div>
          <div class="p-val">{{ $placeOfSupply }}</div>
        </div>

        <div class="particular-item">
          <div class="p-label">SAC Code</div>
          <div class="p-val font-monospace">{{ $sacCode }}</div>
        </div>

        <div class="particular-item">
          <div class="p-label">Tax Treatment</div>
          <div class="p-val">
            @if ($data->tax_type === 'cgst_sgst')
              Intra-State (CGST + SGST)
            @elseif ($data->tax_type === 'igst')
              Inter-State (IGST)
            @elseif ($data->tax_type === 'export_zero_rated')
              Zero-Rated Export (0%)
            @elseif ($data->tax_type === 'unregistered')
              Non-GST / Unregistered
            @else
              Standard Supply
            @endif
          </div>
        </div>

        <div class="particular-item">
          <div class="p-label">Payment Method</div>
          <div class="p-val">{{ $data->payment_gateway ?: 'Online Payment' }}</div>
        </div>
      </div>

      <!-- Line Items Table -->
      <table class="invoice-table">
        <thead>
          <tr>
            <th style="width: 45px;" class="text-center">#</th>
            <th>Item & Description</th>
            <th style="width: 110px;" class="text-center">SAC Code</th>
            <th style="width: 70px;" class="text-center">Qty</th>
            <th style="width: 160px;" class="text-end">Taxable Value</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="text-center text-muted fw-bold">1</td>
            <td>
              @if ($data->deposits_id)
                <div class="fw-bold text-dark">{{ trans('misc.add_funds') }}</div>
                <div class="text-muted small">Account wallet credit</div>
              @elseif ($data->purchases_id)
                @php
                  $purchaseType = $data->purchase()->type ?? '';
                  $purchaseLicense = $data->purchase()->license ?? '';
                @endphp
                <div class="fw-bold text-dark">Stock Photo Purchase</div>
                <div class="text-muted small text-capitalize">{{ $purchaseType }} format • {{ $purchaseLicense }} license</div>
              @elseif ($data->subscriptions_id && $data->subscription())
                <div class="fw-bold text-dark">{{ $planName }}</div>
                <div class="text-muted small">{{ $planInterval }} • Digital media download plan</div>
              @else
                <div class="fw-bold text-dark">Digital Media & AI Content Subscription</div>
                <div class="text-muted small">Online digital services</div>
              @endif
            </td>
            <td class="text-center font-monospace text-muted small">{{ $sacCode }}</td>
            <td class="text-center fw-medium">1</td>
            <td class="text-end fw-bold text-dark font-monospace">
              {{ Helper::formatPrice($subtotal, $invoiceCurrency) }}
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Calculations & Statutory Export Section -->
      <div class="row align-items-start g-4">
        <!-- Left Column: Export LUT or Notes -->
        <div class="col-md-6">
          @if ($isExport)
            <div class="export-lut-card">
              <div class="d-flex align-items-center gap-2 fw-bold mb-1">
                <i class="bi bi-globe-americas"></i>
                <span>Zero-Rated Export Supply</span>
              </div>
              <div>
                Supply meant for export under Letter of Undertaking (LUT) without payment of integrated tax as per Section 16 of the Integrated Goods and Services Tax Act, 2017.
              </div>
              @if (!empty($lutNumber))
                <div class="mt-2 font-monospace fw-semibold">
                  LUT ARN: {{ $lutNumber }}
                </div>
              @endif
            </div>
          @else
            <div class="text-muted small line-height-sm pe-md-4">
              <div class="fw-semibold text-secondary mb-1">Taxation Note:</div>
              @if ($invoiceCurrency === 'INR' && $isGstInvoice)
                <div>Domestic subscription prices are inclusive of applicable Goods and Services Tax (GST). All taxes have been allocated according to Place of Supply rules.</div>
              @else
                <div>Thank you for subscribing to {{ $settings->title }}. For any billing inquiries, invoice revisions, or support, please contact our support team.</div>
              @endif
            </div>
          @endif
        </div>

        <!-- Right Column: Calculation Summary -->
        <div class="col-md-6">
          <div class="calculation-box">
            <div class="calc-row">
              <span>Subtotal (Taxable Value):</span>
              <span class="font-monospace fw-semibold text-dark">{{ Helper::formatPrice($subtotal, $invoiceCurrency) }}</span>
            </div>

            @if ($transactionFee)
              <div class="calc-row">
                <span>{{ trans('misc.handling_fee') }}:</span>
                <span class="font-monospace fw-semibold text-dark">{{ Helper::formatPrice($transactionFee, $invoiceCurrency) }}</span>
              </div>
            @endif

            {{-- GST Split Breakdown --}}
            @if ($isGstInvoice)
              @if ($data->tax_type === 'cgst_sgst')
                <div class="calc-row">
                  <span>Central GST (CGST {{ number_format($data->cgst_rate, 2) }}%):</span>
                  <span class="font-monospace fw-semibold text-dark">{{ Helper::formatPrice($data->cgst_amount, $invoiceCurrency) }}</span>
                </div>
                <div class="calc-row">
                  <span>State GST (SGST {{ number_format($data->sgst_rate, 2) }}%):</span>
                  <span class="font-monospace fw-semibold text-dark">{{ Helper::formatPrice($data->sgst_amount, $invoiceCurrency) }}</span>
                </div>
              @elseif ($data->tax_type === 'igst')
                <div class="calc-row">
                  <span>Integrated GST (IGST {{ number_format($data->igst_rate, 2) }}%):</span>
                  <span class="font-monospace fw-semibold text-dark">{{ Helper::formatPrice($data->igst_amount, $invoiceCurrency) }}</span>
                </div>
              @elseif ($data->tax_type === 'export_zero_rated')
                <div class="calc-row text-muted">
                  <span>GST (0% Export under LUT):</span>
                  <span class="font-monospace">{{ Helper::formatPrice(0, $invoiceCurrency) }}</span>
                </div>
              @elseif ($data->tax_type === 'unregistered')
                <div class="calc-row text-muted">
                  <span>GST (0% Unregistered):</span>
                  <span class="font-monospace">{{ Helper::formatPrice(0, $invoiceCurrency) }}</span>
                </div>
              @endif
            @else
              {{-- Legacy Tax Fallback --}}
              @foreach($taxes as $tax)
                <div class="calc-row">
                  <span>{{ $tax->name }} ({{ $tax->percentage }}%):</span>
                  <span class="font-monospace fw-semibold text-dark">{{ Helper::formatPrice(Helper::calculatePercentage($data->amount, $tax->percentage), $invoiceCurrency) }}</span>
                </div>
              @endforeach
            @endif

            <div class="calc-row total-row">
              <span>Total Paid:</span>
              <span class="font-monospace text-dark">{{ Helper::formatPrice($totalAmount, $invoiceCurrency) }} {{ $invoiceCurrency }}</span>
            </div>

            <div class="mt-3 pt-2 border-top text-end">
              <div class="text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em;">Total Amount in Words</div>
              <div class="fw-semibold text-dark small" style="line-height: 1.4;">
                {{ \App\Services\TaxService::numberToWords((float)$totalAmount, $invoiceCurrency) }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom Signature & Legal Disclaimer -->
      <div class="border-top mt-4 pt-3 text-center text-muted small" style="font-size: 0.775rem;">
        This is a computer-generated tax invoice issued in accordance with applicable tax legislation. No physical signature is required.
      </div>

    </div><!-- /.invoice-paper -->

    <!-- Toast Notification for Link Copy -->
    <div id="copyToast">
      <i class="bi bi-check2-circle text-success me-1"></i> Link copied to clipboard!
    </div>

    <script>
      function copyInvoiceLink() {
        if (navigator.clipboard) {
          navigator.clipboard.writeText(window.location.href).then(showToast);
        } else {
          showToast();
        }
      }

      function copyText(text) {
        if (navigator.clipboard) {
          navigator.clipboard.writeText(text).then(function() {
            showToast("Copied: " + text);
          });
        }
      }

      function showToast(msg) {
        const toast = document.getElementById('copyToast');
        if (msg) toast.innerHTML = '<i class="bi bi-check2-circle text-success me-1"></i> ' + msg;
        toast.style.display = 'block';
        setTimeout(function() {
          toast.style.display = 'none';
        }, 2200);
      }
    </script>
  </body>
</html>
