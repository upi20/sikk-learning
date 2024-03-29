<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Data extends Render_Controller
{
    // Halaman =========================================================================================================
    // Dipakai Administrator | Guru Administrator | Guru |
    public function index()
    {
        // Page Settings
        $this->title = 'Daftar Project';
        $this->title_show = false;
        $this->navigation = ['Data Project'];
        $this->plugins = ['datatables'];

        // Breadcrumb setting
        $this->breadcrumb_1 = 'Dashboard';
        $this->breadcrumb_1_url = base_url();
        $this->breadcrumb_2 = 'Daftar Project';
        $this->breadcrumb_2_url = '#';
        $this->breadcrumb_3 = 'Data Project';
        $this->breadcrumb_3_url = '#';
        $this->breadcrumb_show = false;

        // Administrator
        if ($this->level == 'Administrator') {
            $this->plugins = array_merge($this->plugins, ['select2']);
            $this->content = 'project/data/admin/list';
        }

        // Guru Administrator
        if ($this->level == 'Guru Administrator') {
            $this->data['sekolah'] = $this->sekolah;
            $detail = $this->sekolah->getIdSekolahByIdUser($this->id_user);
            $this->load->model("sekolah/guruModel", 'guru');
            $this->data['list_kelas'] = $this->guru->getKelas($detail['id_sekolah']);
            $this->content = 'project/data/guruadmin/list';
        }

        // Guru Administrator
        if ($this->level == 'Guru') {
            $this->breadcrumb_show = true;
            $this->title_show = true;
            $detail = $this->sekolah->getIdSekolahByIdUser($this->id_user);
            $this->data['detail'] = $detail;
            $this->content = 'project/data/guru/list';
        }

        // Send data to view
        $this->render();
    }

    // Dipakai Administrator  | Guru Administrator | Guru
    public function tambah()
    {
        // Page Settings
        $this->title = 'Tambah Project';
        $this->title_show = false;
        $this->navigation = ['Data Project'];
        $this->plugins = ['summernote', 'summernote-audio'];

        // Breadcrumb setting
        $this->breadcrumb_1 = 'Dashboard';
        $this->breadcrumb_1_url = base_url();
        $this->breadcrumb_2 = 'Daftar Project';
        $this->breadcrumb_2_url = '#';
        $this->breadcrumb_3 = 'Data Project';
        $this->breadcrumb_3_url = base_url('project/data');
        $this->breadcrumb_4 = 'Tambah Data';
        $this->breadcrumb_4_url = '#';

        // content
        if ($this->level == 'Administrator') {
            $this->plugins = array_merge($this->plugins, ['select2']);
            $this->data['id_project'] = $this->model->tambahProject();
            $this->content = 'project/data/admin/tambah';
        }

        // content
        if ($this->level == 'Guru Administrator') {
            $detail = $this->sekolah->getIdSekolahByIdUser($this->id_user);
            $this->load->model("sekolah/guruModel", 'guru');
            $this->data['list_kelas'] = $this->guru->getKelas($detail['id_sekolah']);
            $this->data['id_sekolah'] = $detail['id_sekolah'];
            $this->data['id_project'] = $this->model->tambahProject($detail['id_sekolah']);
            $this->content = 'project/data/guruadmin/tambah';
        }

        // content
        if ($this->level == 'Guru') {
            $detail = $this->sekolah->getIdSekolahByIdUser($this->id_user);
            $this->data['id_sekolah'] = $detail['id_sekolah'];
            $this->data['id_kelas'] = $detail['id_kelas'];
            $this->data['nip_guru'] = $detail['nip_guru'];
            $this->data['id_project'] = $this->model->tambahProject($detail['id_sekolah'], $detail['id_kelas'], $detail['nip_guru']);
            $this->content = 'project/data/guru/tambah';
        }

        // Send data to view
        $this->render();
    }

    // Dipakai Administrator | Guru Administrator | Guru |
    public function perbaiki($id)
    {
        // Page Settings
        $this->title = 'Perbaiki Project';
        $this->title_show = false;
        $this->navigation = ['Data Project'];
        $this->plugins = ['summernote', 'summernote-audio'];

        // Breadcrumb setting
        $this->breadcrumb_1 = 'Dashboard';
        $this->breadcrumb_1_url = base_url();
        $this->breadcrumb_2 = 'Daftar Project';
        $this->breadcrumb_2_url = '#';
        $this->breadcrumb_3 = 'Data Project';
        $this->breadcrumb_3_url = base_url('project/data');
        $this->breadcrumb_4 = 'Perbaiki Project';
        $this->breadcrumb_4_url = '#';

        // content
        if ($this->level == 'Administrator') {
            $detail = $this->model->getProject($id);
            if ($detail) {
                $this->plugins = array_merge($this->plugins, ['select2']);
                $this->data['detail'] = $detail;
                $this->content = 'project/data/admin/perbaiki';
                $this->render();
            } else {
                redirect('my404', 'refresh');
            }
        }

        // content
        if ($this->level == 'Guru Administrator') {
            $detail = $this->model->getProject($id);
            if ($detail) {
                $this->load->model("sekolah/guruModel", 'guru');
                $this->data['list_kelas'] = $this->guru->getKelas($detail['id_sekolah']);
                $this->data['detail'] = $detail;
                $this->content = 'project/data/guruadmin/perbaiki';
                $this->render();
            } else {
                redirect('my404', 'refresh');
            }
        }

        // content
        if ($this->level == 'Guru') {
            $detail = $this->model->getProject($id);
            if ($detail) {
                $this->data['detail'] = $detail;
                $this->content = 'project/data/guru/perbaiki';
                $this->render();
            } else {
                redirect('my404', 'refresh');
            }
        }
    }

    // Fungsi =========================================================================================================
    // Dipakai Administrator | Guru Administrator | Guru |
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
        $count = $this->model->getAllData(null,    null,   null, $_cari, $order, null)->num_rows();
        $this->output_json(['recordsTotal' => $count, 'recordsFiltered' => $count, 'draw' => $draw, 'search' => $_cari, 'data' => $data]);
    }

    // Dipakai Administrator | Guru Administrator | Guru |
    public function getProject()
    {
        $id = $this->input->get("id");
        $result = $this->model->getProject($id);
        $code = $result ? 200 : 500;
        $this->output_json(["data" => $result], $code);
    }

    // Dipakai Administrator | Guru Administrator | Guru |
    public function delete()
    {
        $id = $this->input->post("id");
        $detail = $this->model->getProject($id);
        if ($detail) {
            // Mulai transaksi
            $this->db->trans_start();
            // delete database
            $result = $this->model->delete($id);

            // delete file
            $id_project = $detail['id'];
            $this->files_summernote->delete("/files/$this->path/$id_project/aktifitas");
            $this->files_summernote->delete("/files/$this->path/$id_project");

            $this->db->trans_complete();
            $code = $result ? 200 : 500;
            $this->output_json(["data" => $result], $code);
        } else {
            redirect('my404', 'refresh');
        }
    }

    // Dipakai Administrator | Guru Administrator | Guru |
    public function insert()
    {
        // get data untuk databse
        $judul              = $this->input->post('judul');
        $id_project         = $this->input->post('id_project');
        $id_sekolah         = $this->input->post('id_sekolah');
        $id_kelas           = $this->input->post('id_kelas');
        $nip_guru           = $this->input->post('nip_guru');
        $deskripsi          = $this->input->post('deskripsi', false);
        $pendahuluan        = $this->input->post('pendahuluan', false);
        $tujuan             = $this->input->post('tujuan', false);
        $link_sumber        = $this->input->post('link_sumber', false);
        $jumlah_aktifitas   = $this->input->post('jumlah_aktifitas', false);

        // jka User Level Guru yang kirim
        $detail = $this->sekolah->getIdSekolahByIdUser($this->id_user);
        $nip_guru = $nip_guru != null ? $nip_guru : $detail['nip_guru'];
        $id_sekolah = $id_sekolah != null ? $id_sekolah : $detail['id_sekolah'];
        $id_kelas = $id_kelas != null ? $id_kelas : $detail['id_kelas'];

        // list files yang dikirim
        $images                     = $this->input->post("image");
        $images                     = $images == null ? [] : $images;
        $audios                     = $this->input->post("audio");
        $audios                     = $audios == null ? [] : $audios;
        // Simpan file yang digunakan dan hapus file yang tidak digunakan
        $simpan = $this->files_summernote->simpanData("/files/$this->path/$id_project", $images, $audios);

        $exe = $this->model->simpanData($id_project, $id_sekolah, $id_kelas, $nip_guru, $judul, $pendahuluan, $deskripsi, $tujuan, $link_sumber, $jumlah_aktifitas, $simpan['audio'], $simpan['image']);
        $this->output_json(["status" => $exe]);
    }

    // Dipakai Administrator | Guru Administrator | Guru |
    public function upload()
    {
        $tipe = $this->input->post('tipe');
        $id_project = $this->input->post('id_project');
        $path = "/files/$this->path/$id_project/$tipe";
        $result = $this->files_summernote->upload($path, $tipe);

        if ($result) {
            $file_name = $this->upload->data("file_name");
            $this->output_json([
                'path' => "$path/$file_name",
                'file_name' => $file_name
            ]);
        } else {
            $this->output_json([
                'message' => $this->upload->display_errors()
            ], 400);
        }
    }

    function __construct()
    {
        parent::__construct();
        // Cek session
        $this->sesion->cek_session();
        $this->level = $this->session->userdata('data') ? $this->session->userdata('data')['level'] : '';
        $this->id_user = $this->session->userdata('data') ? $this->session->userdata('data')['id'] : '';

        // cek level
        if ($this->level != 'Guru' && $this->level != 'Administrator' && $this->level != 'Guru Administrator') {
            redirect('my404', 'refresh');
        }
        $this->load->model("sekolah/DaftarSekolahModel", 'sekolah');
        $this->load->model("project/DataModel", 'model');
        $this->default_template = 'templates/dashboard';
        $this->load->library('plugin');
        $this->load->helper('url');
        $this->path = 'project/data';
    }
}
