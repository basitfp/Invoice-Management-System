<div class="modal fade" id="areaConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="areaConfirmForm" method="POST" action="#">
                @csrf
                <div id="area-method-field"></div>
                <input type="hidden" id="area-confirm-id">

                <div class="modal-header">
                    <h5 class="modal-title" id="area-confirm-title">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p id="area-confirm-body" class="mb-0"></p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" id="area-confirm-submit" class="btn">
                        Confirm
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>