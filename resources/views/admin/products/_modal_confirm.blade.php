{{--
    Confirm Modal — handles two actions:
    1. Toggle Status  => PATCH  /admin/products/{id}/toggle-status
    2. Delete Product => DELETE /admin/products/{id}
    JS dynamically sets action, method, title, and body text.
--}}

<div class="modal fade" id="productConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content">

            <div id="confirm-method-field"></div>
            <input type="hidden" id="confirm-id">
            <input type="hidden" id="confirm-action">

            <div class="modal-header">
                <h5 class="modal-title" id="confirm-title">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <p id="confirm-body" class="mb-0" style="color: var(--text-secondary); font-size: 14px; line-height: 1.6;"></p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirm-submit-btn" class="btn btn-danger">
                    <span id="confirm-btn-text">Confirm</span>
                    <span id="confirm-btn-spinner" class="spinner-border spinner-border-sm ms-1" role="status" style="display: none;"></span>
                </button>
            </div>

        </div>
    </div>
</div>
