{{--
    Yeh modal 2 kaam karta hai:
    1. Toggle Status  => PATCH  /categories/{id}/toggle-status
    2. Delete         => DELETE /categories/{id}
    JS se action aur method dinamically set hota hai
--}}

<div class="modal fade" id="categoryConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="categoryConfirmForm" method="POST" action="#">
                @csrf
                {{-- JS yahan PATCH ya DELETE method inject karega --}}
                <div id="category-method-field"></div>
                <input type="hidden" id="confirm-id">

                <div class="modal-header">
                    <h5 class="modal-title" id="category-confirm-title">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p id="category-confirm-body" class="mb-0"></p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal"
                        style="height: 48px; border-radius: 10px; font-weight: 600;">
                        Cancel
                    </button>
                    <button type="button" id="category-confirm-submit" class="btn px-4"
                        style="height: 48px; border-radius: 10px; font-weight: 600;">
                        Confirm
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>