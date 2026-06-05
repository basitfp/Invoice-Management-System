{{--
    This modal handles two actions:
    1. Toggle Status  => PATCH  /vendors/{id}/toggle-status
    2. Delete         => DELETE /vendors/{id}
    JS dynamically sets the action and _method field.
--}}

<div class="modal fade" id="vendorConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="vendorConfirmForm" method="POST" action="#">
                @csrf
                {{-- JS will inject PATCH or DELETE here --}}
                <div id="vendor-method-field"></div>
                <input type="hidden" id="vendor-confirm-id">

                <div class="modal-header">
                    <h5 class="modal-title" id="vendor-confirm-title">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p id="vendor-confirm-body" class="mb-0"></p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal"
                        style="height: 48px; border-radius: 10px; font-weight: 600;">
                        Cancel
                    </button>
                    <button type="button" id="vendor-confirm-submit" class="btn px-4"
                        style="height: 48px; border-radius: 10px; font-weight: 600;">
                        Confirm
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>