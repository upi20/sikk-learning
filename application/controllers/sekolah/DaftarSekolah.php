<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DaftarSekolah extends Render_Controller
{

    // dipakai Administrator |
    public function index()
    {
        // Page Settings
        $this->title = 'Daftar Sekolah';
        $this->navigation = ['Sekolah', 'Daftar Sekolah'];
        $this->plugins = ['datatables'];

        // Breadcrumb setting
        $this->breadcrumb_1 = 'Dashboard';
        $this->breadcrumb_1_url = base_url();
        $this->breadcrumb_2 = 'Daftar Sekolah';
        $this->breadcrumb_2_url = '#';

        // content
        $this->content      = 'sekolah/daftar-sekolah';

        // Send data to view
        $this->render();
    }

    // dipakai Administrator |
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

        $data = $this->model->getAllData($draw, $length, $start, $_cari, $order)->result_array();
        $count = $this->model->getAllData(null, null, null, $_cari, $order, null)->num_rows();

        $this->output_json(['recordsTotal' => $count, 'recordsFiltered' => $count, 'draw' => $draw, 'search' => $_cari, 'data' => $data]);
    }

    // dipakai Administrator |
    public function getSekolah()
    {
        $id = $this->input->get("id");
        $result = $this->model->getSekolah($id);
        $code = $result ? 200 : 500;
        $this->output_json(["data" => $result], $code);
    }

    // dipakai Administrator |
    public function getSekolahDetail()
    {
        $id = $this->input->get("id");
        $result = $this->model->getSekolahDetail($id);
        $code = $result ? 200 : 500;
        $this->output_json(["data" => $result], $code);
    }

    // dipakai Administrator |
    public function insert()
    {
        $npsn = trim($this->input->post("npsn"));
        $nama = trim($this->input->post("nama"));
        $alamat = trim($this->input->post("alamat"));
        $no_telepon = trim($this->input->post("no_telepon"));
        $status = trim($this->input->post("status"));
        $result = $this->model->insert($npsn, $nama, $alamat, $no_telepon, $status);
        $code = $result ? 200 : 500;
        $this->output_json(["data" => $result], $code);
    }

    // dipakai Administrator |
    public function update()
    {
        $id = $this->input->post("id");
        $npsn = trim($this->input->post("npsn"));
        $nama = trim($this->input->post("nama"));
        $alamat = trim($this->input->post("alamat"));
        $no_telepon = trim($this->input->post("no_telepon"));
        $status = trim($this->input->post("status"));
        $result = $this->model->update($id, $npsn, $nama, $alamat, $no_telepon, $status);
        $code = $result ? 200 : 500;
        $this->output_json(["data" => $result], $code);
    }

    // dipakai Administrator |
    public function delete()
    {
        $id = $this->input->post("id");
        $result = $this->model->delete($id);
        $code = $result ? 200 : 500;
        $this->output_json(["data" => $result], $code);
    }

    // dipakai Registrasi |
    public function cari()
    {
        $key = $this->input->post('q');
        // jika inputan ada
        if ($key) {
            $this->output_json([
                "results" => $this->model->cari($key)
            ]);
        } else {
            $this->output_json([
                "results" => []
            ]);
        }
    }

    // dipakai administrator |
    public function cekNpsn()
    {
        $npsn = $this->input->post('npsn');
        $result = $this->model->cekNpsn($npsn);
        $this->output_json($result);
    }

    function __construct()
    {
        parent::__construct();
        // Cek session
        $this->sesion->cek_session();
        if ($this->session->userdata('data')['level'] != 'Administrator') {
            redirect('my404', 'refresh');
        }

        $this->load->model("sekolah/DaftarSekolahModel", 'model');
        $this->default_template = 'templates/dashboard';
        $this->load->library('plugin');
        $this->load->helper('url');
    }
}

/* End of file Pengguna.php */
/* Location: ./application/controllers/pengaturan/Pengguna.php */