<?php
$level = $this->session->userdata('data')['level'];
$label = '';
$setFilterSekolah = '';
$setFilterKelas = '';

if ($level == 'Guru Administrator' || $level == 'Guru') {
    $setFilterSekolah = 'style="display:none"';
}
?>
<div class="card card-info card-outline" id="filter">
    <div class="card-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-end  align-items-star w-100 flex-md-row flex-column">
                <h3 class="card-title align-self-center">Filter Kelas: </h3>

                <div class="form-group  mb-lg-0 ml-lg-2" <?= $setFilterSekolah ?>>
                    <select class="form-control" id="filter-sekolah" name="filter-sekolah" style="width: 100%;">
                        <?php if ($level == 'Administrator') : ?>
                            <option value="" selected>Semua Sekolah</option>
                        <?php endif ?>
                        <?php foreach ($sekolah as $s) {
                            echo '<option value="' . $s['id'] . '">' . $s['nama'] . '</option>';
                        } ?>
                    </select>
                </div>

                <div class="form-group  mb-lg-0 ml-lg-2">
                    <select class="form-control" id="filter-aktif" name="filter-aktif">
                        <option value="">Status</option>
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>

                <div class="form-group  mb-lg-0 ml-lg-2">
                    <input type="text" class="form-control" id="filter-key" name="filter-key" placeholder="Kata Kunci" required />
                </div>

                <div class=" ml-lg-2">
                    <button type="button" class="btn btn-info btn" id="btn-filter" style="min-width: 72px;"><i class="fas fa-search"></i></i> Cari</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between w-100">
            <h3 class="card-title" id="table-title">List Kelas</h3>
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal" id="btn-tambah"><i class="fa fa-plus"></i> Tambah</button>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="dt_basic" class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <?php if ($level == 'Administrator') : ?>
                        <th>Sekolah</th>
                    <?php endif ?>
                    <th>Nama Kelas</th>
                    <th>Jumlah Murid</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
    <!-- /.card-body -->
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="sekolah">Sekolah</label>
                                <select class="form-control" id="sekolah" name="sekolah" style="width: 100%;">
                                    <?php foreach ($sekolah as $s) {
                                        echo '<option value="' . $s['id'] . '">' . $s['nama'] . '</option>';
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="nama">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Kelas" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        Submit
                    </button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </div><!-- /.modal-content -->
        </form>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    const level = '<?= $level ?>';
</script>