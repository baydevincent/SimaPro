<div class="modal fade" id="modalEditWorker" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <form id="formEditWorker">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_worker_id" name="worker_id">
                <input type="hidden" id="edit_project_id" name="project_id" value="{{ $project->id ?? '' }}">
                
                <div class="modal-header">
                    <h5 class="modal-title">Edit Karyawan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <div class="alert alert-danger d-none" id="editErrorBox"></div>
                    <div class="alert alert-success d-none" id="editSuccessBox"></div>

                    <div class="form-group">
                        <label>Nama <span class="text-danger">*</span></label>
                        <input type="text" name="nama_worker" id="edit_nama_worker" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <label>Posisi</label>
                        <input type="text" name="posisi" id="edit_posisi" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <label>No Handphone</label>
                        <input type="text" name="no_hp" id="edit_no_hp" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" name="aktif" id="edit_aktif" class="form-check-input" value="1">
                            <label class="form-check-label" for="edit_aktif">Aktif</label>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" id="btnUpdateWorker" class="btn btn-primary">
                        Update
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
