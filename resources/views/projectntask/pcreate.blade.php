<div class="modal fade" id="modalCreateProject" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

        <form id="formCreateProject">

            <div class="modal-header">
                <h5 class="modal-title">Tambah Master Project</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">

                <div class="alert alert-danger d-none" id="errorBox"></div>

                <div class="form-group">
                    <label>Nama Project</label>
                    <input type="text" name="nama_project" class="form-control">
                    <div class="invalid-feedback"></div>
                </div>

                <div class="form-group">
                    <label>Nilai Project (Rp)</label>
                    <input type="number" name="nilai_project" class="form-control">
                    <div class="invalid-feedback"></div>
                </div>

                <div class="form-group">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control">
                    <div class="invalid-feedback"></div>
                </div>

                <div class="form-group">
                    <label>Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control">
                    <div class="invalid-feedback"></div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Batal
                </button>
                <button type="submit" class="btn btn-success">
                    Simpan Project
                </button>
            </div>

        </form>

        </div>
    </div>
</div>