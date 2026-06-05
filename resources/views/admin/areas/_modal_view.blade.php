<div class="modal fade" id="areaViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Area Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="d-flex flex-column gap-3">

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary fw-medium">Area ID:</span>
                        <span class="fw-semibold text-dark" id="view-area-id">-</span>
                    </div>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary fw-medium">Area Name:</span>
                        <span class="fw-semibold text-dark" id="view-area-name">-</span>
                    </div>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary fw-medium">Status:</span>
                        <span id="view-area-status">-</span>
                    </div>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary fw-medium">Created Date:</span>
                        <span class="fw-semibold text-dark" id="view-area-created-at">-</span>
                    </div>

                    <div class="d-flex justify-content-between py-2">
                        <span class="text-secondary fw-medium">Updated Date:</span>
                        <span class="fw-semibold text-dark" id="view-area-updated-at">-</span>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal"
                    style="height: 48px; border-radius: 10px; font-weight: 600;">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>