<div class="modal fade" id="statusConfirmModal" tabindex="-1" aria-hidden="true" aria-labelledby="statusConfirmTitle">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="#" method="POST" id="statusConfirmForm">
                @csrf
                <input type="hidden" name="_method" value="PATCH">
                <div class="modal-header">
                    <h5 class="modal-title" id="status-confirm-title">Change Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="status-confirm-body">Are you sure you want to change the status of this category?</p>
                </div>
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary px-4"
                            data-bs-dismiss="modal"
                            style="height: 48px; border-radius: 10px; font-weight: 600;">
                        Cancel
                    </button>
                    <button type="submit"
                            class="btn px-4"
                            id="status-confirm-submit"
                            style="height: 48px; border-radius: 10px; font-weight: 600;">
                        Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>