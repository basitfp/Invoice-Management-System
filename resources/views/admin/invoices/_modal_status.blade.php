{{-- Invoice Status Modal --}}
<style>
.status-option-label {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    border: 2px solid var(--border-color, #e2e8f0);
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
}
.status-option-label:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
}
.status-option-label.active {
    border-color: var(--primary-color, #3b82f6);
    background: #eff6ff;
}
.status-indicator {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 2px solid #cbd5e1;
    position: relative;
    transition: all 0.2s ease;
    flex-shrink: 0;
}
.status-option-label.active .status-indicator {
    border-color: var(--primary-color, #3b82f6);
}
.status-option-label.active .status-indicator::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 10px;
    height: 10px;
    background-color: var(--primary-color, #3b82f6);
    border-radius: 50%;
}
</style>

<div class="modal fade" id="invoiceStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; border:1px solid var(--border-color); overflow:hidden;">

            <form id="invoiceStatusForm" method="POST" action="#">
                @csrf
                @method('PATCH')

                {{--
                    FIX: This hidden field name="status" is what the controller validates.
                    invoice-list.js populates it via $('#status-value').val(selectedStatus)
                    before FormData is built, so the correct value is always submitted.
                --}}
                <input type="hidden" name="status" id="status-value">

                <div class="modal-header" style="background:#fafbfc; border-bottom:1px solid var(--border-color); padding:20px 24px;">
                    <h5 class="modal-title" style="font-size:16px; font-weight:700; color:var(--text-primary);">
                        <i class="bi bi-arrow-repeat me-2" style="color:var(--primary-color);"></i>
                        Update Invoice Status
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body" style="padding:24px;">
                    <p class="mb-3" id="status-modal-invoice-number"
                        style="font-size:13px; font-weight:600; color:var(--text-secondary);"></p>
                    <label class="invoice-label">Select New Status</label>
                    <div class="d-flex flex-column gap-2 mt-2" id="status-options">

                        <label class="status-option-label" data-value="draft">
                            <input type="radio" name="status_pick" value="draft" class="d-none">
                            <div class="status-indicator me-3"></div>
                            <span class="invoice-status-draft">Draft</span>
                        </label>

                        <label class="status-option-label" data-value="paid">
                            <input type="radio" name="status_pick" value="paid" class="d-none">
                            <div class="status-indicator me-3"></div>
                            <span class="invoice-status-paid">Paid</span>
                        </label>

                        <label class="status-option-label" data-value="unpaid">
                            <input type="radio" name="status_pick" value="unpaid" class="d-none">
                            <div class="status-indicator me-3"></div>
                            <span class="invoice-status-sent">Unpaid</span>
                        </label>

                        <label class="status-option-label" data-value="due">
                            <input type="radio" name="status_pick" value="due" class="d-none">
                            <div class="status-indicator me-3"></div>
                            <span class="invoice-status-cancelled">Due</span>
                        </label>

                    </div>
                    <span class="field-error text-danger small mt-2 d-block" id="status-pick-error"></span>
                </div>

                <div class="modal-footer" style="border-top:1px solid var(--border-color); padding:16px 24px; background:#fafbfc;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                        style="height:44px; border-radius:10px; font-weight:600; padding:0 20px;">
                        Cancel
                    </button>
                    <button type="button" id="confirmStatusBtn" class="btn btn-primary"
                        style="height:44px; border-radius:10px; font-weight:600; padding:0 24px;">
                        Update Status
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>