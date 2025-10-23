<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header card-header-sea-light">
        <h5 class="modal-title" id="deleteModalLabel"><i class="fas fa-exclamation-triangle text-danger me-2"></i> Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this record? This action cannot be undone.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-admin-light" data-bs-dismiss="modal">Cancel</button>
        <form method="POST" action="#">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-admin-primary">
                <i class="fas fa-trash me-2"></i> Delete
            </button>
        </form>
      </div>
    </div>
  </div>
</div>