<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Guru extends Render_Controller
{

    // dipakai Administrator | Guru Administrator
    public function index()
    {
        // Page Settings
        $this->title = 'Kelas';
        $this->title_show = false;
        $this->navigation = ['Guru '];
        $this->plugins = ['datatables', 'select2'];

        // Breadcrumb setting
        $this->breadcrumb_show = false;

        $this->data['level'] = $this->level;
        $this->data['sekolah'] = $this->model->getAllSekolah();

        // content
        $this->content      = 'sekolah/guru';

        // Send data to view
        $this->render();
    }

    // dipakai Administrator | Guru Administrator
    public function ajax_data()
    {
        $order = ['order' => $this->input->post('order'), 'columns' => $this->input->post('columns')];
        $start = $this->input->post('start');
        $draw = $this->input->post('draw');
        $draw = $draw == null ? 1 : $draw;
        $length = $this->input->post('length');
        $cari = $this->input->post('search');


        if (isset($cari['value'])) {
            $_cari = $cari['value'];
        } else {
            $_cari = null;
        }

        // cek filter
        $filter = $this->input->post("filter");

        $data = $this->model->getAllData($draw, $length, $start, $_cari, $order, $filter)->result_array();
        $count = $this->model->getAllData(null, null,    null,   $_cari, $order, $filter)->num_rows();

        $this->output_json(['recordsTotal' => $count, 'recordsFiltered' => $count, 'draw' => $draw, 'search' => $_cari, 'data' => $data]);
    }

    // dipakai Administrator | Guru Administrator
    public function getKelas()
    {
        $id = $this->input->get("id_sekolah");
        $result = $this->model->getKelas($id);
        $code = $result ? 200 : 500;
        $this->output_json(["data" => $result], $code);
    }

    // dipakai Administrator | Guru Administrator
    public function getGuru()
    {
        $nip = $this->input->get("nip");
        $result = $this->model->getGuru($nip);
        $code = $result ? 200 : 500;
        $this->output_json(["data" => $result], $code);
    }

    // dipakai Administrator | Guru Administrator
    public function insert()
    {
        // load model pengguna untuk insert
        $this->load->model('pengaturan/penggunaModel', 'pengguna');

        // Mulai transaksi
        $this->db->trans_start();
        // insert user
        $status = $this->input->post("status");
        $level = $this->input->post("level");
        $nama = $this->input->post("nama");
        $no_telpon = $this->input->post("no_telpon");
        $username = $this->input->post("nip");
        $password = $this->input->post("password");
        $user = $this->pengguna->insert($level, $nama, $no_telpon, $username, $password, $status);

        // insert guru
        $id_user = $user['id'];
        $tanggal_lahir = $this->input->post("tanggal_lahir");
        $nip = $this->input->post("nip");
        $jenis_kelamin = $this->input->post("jenis_kelamin");
        $alamat = $this->input->post("alamat");
        $id_sekolah = $this->input->post("id_sekolah");
        $guru = $this->model->insertGuru($nip, $id_user, $id_sekolah, $nama, $tanggal_lahir, $jenis_kelamin, $alamat, $no_telpon, $status);

        // insert guru_kelas
        $id_kelas = $this->input->post("id_kelas");
        $guru_kelas = $this->model->insertGuruKelas($nip, $id_kelas, $status);

        // simpan transaksi
        $this->db->trans_complete();
        $result = $user && $guru && $guru_kelas;

        // kirim output
        $code = $result ? 200 : 500;
        $this->output_json(["data" => $result], $code);
    }

    // dipakai Administrator | Guru Administrator
    public function update()
    {
        // load model pengguna untuk update
        $this->load->model('pengaturan/penggunaModel', 'pengguna');

        // Mulai transaksi
        $this->db->trans_start();
        // insert user
        // level guru 5 di databasee
        $id = $this->input->post("id");
        $status = $this->input->post("status");
        $level = $this->input->post("level");
        $nama = $this->input->post("nama");
        $no_telpon = $this->input->post("no_telpon");
        $username = $this->input->post("nip");
        $password = $this->input->post("password");
        $user_detail = $this->model->getUsers($id);

        $user = $this->pengguna->update($user_detail['id_user'], $level, $nama, $no_telpon, $username, $password, $status);

        // insert guru
        $tanggal_lahir = $this->input->post("tanggal_lahir");
        $nip = $this->input->post("nip");
        $jenis_kelamin = $this->input->post("jenis_kelamin");
        $alamat = $this->input->post("alamat");
        $id_sekolah = $this->input->post("id_sekolah");
        $guru = $this->model->updateGuru($id, $nip, $user_detail['id_user'], $id_sekolah, $nama, $tanggal_lahir, $jenis_kelamin, $alamat, $no_telpon, $status);

        // insert guru_kelas
        $id_kelas = $this->input->post("id_kelas");
        $guru_kelas = $this->model->updateGuruKelas($user_detail['id_guru_kelas'], $nip, $id_kelas, $status);

        // simpan transaksi
        $this->db->trans_complete();
        $result = $user && $guru && $guru_kelas;

        // kirim output
        $code = $result ? 200 : 500;
        $this->output_json(["data" => $result], $code);
    }

    // dipakai Administrator | Guru Administrator
    public function delete()
    {
        // load model pengguna untuk update
        $this->load->model('pengaturan/penggunaModel', 'pengguna');
        $id = $this->input->post("id");
        $user_detail = $this->model->getUsers($id);

        // Mulai transaksi
        $this->db->trans_start();
        // delete user
        $user = $this->pengguna->delete($user_detail['id_user']);

        // delete guru
        $guru = $this->model->deleteguru($id);

        // delete guru kelas
        $guru_kelas = $this->model->deleteguruKelas($user_detail['id_guru_kelas']);

        // simpan transaksi
        $this->db->trans_complete();
        $result = $user && $guru && $guru_kelas;
        $code = $result ? 200 : 500;
        $this->output_json(["data" => $result], $code);
    }

    // dipakai Administrator | Guru Administrator
    public function cekNip()
    {
        $nip = $this->input->get("nip");
        $result = $this->model->cekNip($nip);
        $this->output_json(["data" => $result]);
    }


    function __construct()
    {
        parent::__construct();
        // Cek session
        $this->sesion->cek_session();
        $this->level = $this->session->userdata('data')['level'];
        if ($this->level != 'Administrator' && $this->level != 'Guru Administrator') {
            redirect('my404', 'refresh');
        }

        $this->load->model("sekolah/guruModel", 'model');
        $this->default_template = 'templates/dashboard';
        $this->load->library('plugin');
        $this->load->helper('url');
    }
}

/* End of file Pengguna.php */
/* Location: ./application/controllers/pengaturan/Pengguna.php */