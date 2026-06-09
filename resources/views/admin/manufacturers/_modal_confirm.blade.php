{{--
    This modal handles two actions:
    1. Toggle Status  => PATCH  /manufacturers/{id}/toggle-status
    2. Delete         => DELETE /manufacturers/{id}
    JS dynamically sets the action and _method field.
--}}

<div class="modal fade" id="manufacturerConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="manufacturerConfirmForm" method="POST" action="#">
                @csrf
                {{-- JS will inject PATCH or DELETE here --}}
                <div id="manufacturer-method-field"></div>
                <input type="hidden" id="manufacturer-confirm-id">

                <div class="modal-header">
                    <h5 class="modal-title" id="manufacturer-confirm-title">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p id="manufacturer-confirm-body" class="mb-0"></p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" id="manufacturer-confirm-submit" class="btn px-4">
                        Confirm
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>