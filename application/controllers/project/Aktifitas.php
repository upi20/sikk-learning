<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Aktifitas extends Render_Controller
{
    // Dipakai Administrator | Guru Administrator | Guru |
    public function detail($id)
    {

        // 1. Get jml aktifitas project [sudah]
        // 2. Get aktifitas project jika kurang maka buat.[sudah]
        // 3. buat upload handeler
        // 4. buat display
        // 5. simpan

        // Page Settings
        $this->title = 'Aktifitas Project';
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
        $this->breadcrumb_4 = 'Aktifitas Project';
        $this->breadcrumb_4_url = '#';
        $this->breadcrumb_show = false;

        $detail = $this->model->getProject($id);
        if ($detail) {
            $this->data['detail'] = $detail;
            $this->data['templates'] = $this->model->getListTemplates($detail['id_sekolah']);
            $this->data['list_aktifitas'] = $this->model->getListAktifitas($id);
            $this->content = 'project/data/aktifitas';
            $this->render();
        } else {
            redirect('my404', 'refresh');
        }
    }

    public function tambahAktifitas()
    {
        $id_sekolah = $this->input->post("id_sekolah");
        $id_project = $this->input->post("id_project");
        $list_template = $this->model->getListTemplates($id_sekolah);
        $id_project_baru = $this->model->creteAktifitas($id_project);
        $this->output_json(['id_project' => $id_project_baru, 'list_template' => $list_template]);
    }

    public function upload()
    {
        $tipe = $this->input->post('tipe');
        $id_project = $this->input->post('id_project');
        $path = "/files/$this->path/$id_project/aktifitas/$tipe";

        // cek directory
        if (!is_dir('.' . $path)) {
            mkdir('.' . $path, 0755, TRUE);
        }

        $config['upload_path']          = '.' . $path;
        if ($tipe == "image") {
            $config['allowed_types']        = 'gif|jpg|png|jpeg|JPG|PNG|JPEG';
        } else if ($tipe == "audio") {
            $config['allowed_types']        = 'opus|flac|webm|weba|wav|ogg|m4a|mp3|oga|mid|amr|aiff|wma|au|aac|OPUS|FLAC|WEBM|WEBA|WAV|OGG|M4A|MP3|OGA|MID|AMR|AIFF|WMA|AU|AAC';
        }
        $megabit = 1024;
        $maxmb   = 10; // max file upload 10 mb
        $maxsize = $megabit * $maxmb;

        $config['overwrite']            = false;
        $config['max_size']             = $maxsize;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        $result = $this->upload->do_upload($tipe);
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

    public function insert()
    {
        // get data untuk databse
        $id_project                 = $this->input->post('id_project');

        // list files yang dikirim
        $images                     = $this->input->post("image");
        $images                     = $images == null ? [] : $images;
        $audios                     = $this->input->post("audio");
        $audios                     = $audios == null ? [] : $audios;
        // Simpan ke database ==========================================================================================
        $simpan_image = '';
        foreach ($images as $image) {
            $simpan_image .= ($simpan_image == '') ? $image : ('|' . $image);
        }

        $simpan_audio = '';
        foreach ($audios as $audio) {
            $simpan_audio .= ($simpan_audio == '') ? $audio : ('|' . $audio);
        }

        $list_aktifitas = json_decode($this->input->post('aktifitas', false), true);
        $result = true;
        $simpan_hapus_aktifitas = true;
        // Mulai transaksi
        $this->db->trans_start();
        $id_simpan = [];
        foreach ($list_aktifitas as $id_aktifitas => $aktifitas) {
            $id_simpan[] = $id_aktifitas;
            $judul = $aktifitas['judul'];
            $jenis_upload = $aktifitas['jenis_upload'];
            $nilai = $aktifitas['nilai'];
            $template = $aktifitas['template'];
            $naskah = $aktifitas['naskah'];
            $detail = $aktifitas['detail'];
            $lembar_kerja = $aktifitas['lembar_kerja'];

            $exe = $this->model->simpanData($id_aktifitas, $judul, $jenis_upload, $nilai, $template, $naskah, $detail, $lembar_kerja, $simpan_audio, $simpan_image);
            if (!$exe) {
                $result = false;
            }
        }
        // simpan transaksi
        // hapus aktifitas yang tidak disimpan
        $list_aktifitas_database = $this->model->getListAktifitasForDelete($id_project);
        foreach ($list_aktifitas_database as $list) {
            if (!in_array($list['id'], $id_simpan)) {
                $exe = $this->model->deleteAktifitas($list['id']);
                if (!$exe) {
                    $simpan_hapus_aktifitas = false;
                }
            }
        }
        $simpan_jumlah = $this->model->updateJumlahAktifitas($id_project, count($id_simpan));

        $this->db->trans_complete();
        // =============================================================================================================


        // clear file ==================================================================================================
        // list file in dir
        $this->load->helper('directory');
        $id_project = $this->input->post('id_project');
        $path = "/files/$this->path/$id_project/aktifitas";

        $images_dir = directory_map(".$path/image", FALSE, TRUE);
        $audios_dir = directory_map(".$path/audio", FALSE, TRUE);

        // delete file tidak terpakai
        if ($images_dir) {
            foreach ($images_dir as $file) {
                if (!in_array($file, $images)) {
                    $this->deleteFile(".$path/image/" . $file);
                }
            }
        }

        if ($audios_dir) {
            foreach ($audios_dir as $file) {
                if (!in_array($file, $audios)) {
                    $this->deleteFile(".$path/audio/" . $file);
                }
            }
        }

        // jika tidak ada file maka folder akan dihapus
        if ($images_dir == false || $images == false) {
            if (is_dir(".$path/image")) {
                rmdir(".$path/image");
            }
        }

        if ($audios_dir == false || $audios == false) {
            if (is_dir(".$path/audio")) {
                rmdir(".$path/audio");
            }
        }
        // =============================================================================================================
        $this->output_json(["status" => $result && $simpan_hapus_aktifitas && $simpan_jumlah]);
    }

    private function deleteFile($path)
    {
        $result = false;
        if (file_exists($path)) {
            $result = unlink($path);
        }
        return $result;
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
        if ($this->level == 'Guru' ||  $this->level == 'Guru Administrator') {
            $this->load->model("sekolah/DaftarSekolahModel", 'sekolah');
        }

        $this->load->model("project/AktifitasModel", 'model');
        $this->default_template = 'templates/dashboard';
        $this->load->library('plugin');
        $this->load->helper('url');
        $this->path = 'project/data';
    }
}
