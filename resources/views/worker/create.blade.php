<div class="modal fade" id="modalCreateWorker" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <form id="formCreateWorker"
                data-action="{{ route('project.workers.store', $project->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Karyawan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <div class="alert alert-danger d-none" id="errorBox"></div>

                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="nama_worker" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <label>Jabatan</label>
                        <input type="text" name="jabatan" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <label>No Handphone</label>
                        <input type="number" name="no_hp" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>