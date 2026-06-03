{{--
    Yeh modal 2 kaam karta hai:
    1. Toggle Status  => PATCH  /customers/{id}/toggle
    2. Delete         => DELETE /customers/{id}
    JS se action aur method dinamically set hota hai
--}}

<div class="modal fade" id="customerConfirmModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="customerConfirmForm" method="POST" action="#">
                @csrf
                <div id="customer-method-field"></div>
                <input type="hidden" id="confirm-id">

                <div class="modal-header">
                    <h5 class="modal-title" id="customer-confirm-title">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p id="customer-confirm-body" class="mb-0"></p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="customer-confirm-submit" class="btn btn-danger">Confirm</button>
                </div>

            </form>
        </div>
    </div>
</div>