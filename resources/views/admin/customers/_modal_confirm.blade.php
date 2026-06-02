<div class="modal fade" id="customerConfirmModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="customerConfirmForm" method="POST">
                @csrf
                @method('PATCH')

                <div class="modal-header">
                    <h5 id="confirm-title">Confirm Action</h5>
                </div>

                <div class="modal-body">
                    <p id="confirm-body"></p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm</button>
                </div>
            </form>

        </div>
    </div>
</div>