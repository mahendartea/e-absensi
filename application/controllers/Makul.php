<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Makul extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('my_model');
		$this->load->helper(array('form', 'url'));
		$this->load->library('user_agent');
		ini_set('date.timezone', 'Asia/Jakarta');
		if (!$this->session->userdata('id_user') and $this->session->userdata('id_user') != "0") {
			$this->session->set_flashdata("msg", "<br/><div class='alert alert-info' role='alert'>
			<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
			<strong></strong> Silahkan login terlebih dahulu.
			</div>");
			redirect('login');
		}
	}

	function hapus()
	{
		$IDSET = $this->uri->segment(3);
		$where = array('IDSET' => $IDSET);
		if ($this->my_model->hapus("atur_bahan_ajar", $where)) {
			$this->session->set_flashdata("msg", "<br/><div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Data berhasil dihapus.</div>");
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			$this->session->set_flashdata("msg", "<br/><div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Data Gagal dihapus. Coba lagi.</div>");
			redirect($_SERVER['HTTP_REFERER']);
		}
	}


	public function entri()
	{
		$PERTEMUAN = trim($this->security->xss_clean($this->input->post('pertemuan')));
		$MATERI = trim($this->security->xss_clean($this->input->post('materi')));
		$IDMAKUL = trim($this->security->xss_clean($this->uri->segment(3)));
		$THSHM = trim($this->security->xss_clean($this->uri->segment(4)));
		$IDPRODI = trim($this->security->xss_clean($this->uri->segment(5)));
		$NAMAKLS = trim($this->security->xss_clean($this->uri->segment(6)));
		$SEMESTER = trim($this->security->xss_clean($this->uri->segment(7)));
		$CREATED = date("Y-m-d H:i:s", time());
		$where = array('IDDOSEN' => $this->session->userdata('id_user'), 'THSHM' => $THSHM, 'IDPRODI' => $IDPRODI, 'IDMAKUL' => $IDMAKUL, 'NAMAKLS' => $NAMAKLS, 'SEMESTER' => $SEMESTER, 'PERTEMUAN' => $PERTEMUAN, 'MATERI' => $MATERI);
		$GetBahanAjar = $this->my_model->cek_data("atur_bahan_ajar", $where);
		if ($GetBahanAjar->num_rows() >= 1) {
			$this->session->set_flashdata("msg", "<br/><div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Bahan Ajar ini sudah ditambahkan di pertemuan ini. Silahkan pilih bahan ajar yang lain..!</div>");
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			$data = array('IDDOSEN' => $this->session->userdata('id_user'), 'THSHM' => $THSHM, 'IDPRODI' => $IDPRODI, 'IDMAKUL' => $IDMAKUL, 'NAMAKLS' => $NAMAKLS, 'SEMESTER' => $SEMESTER, 'PERTEMUAN' => $PERTEMUAN, 'MATERI' => $MATERI, 'CREATED' => $CREATED);
			if ($this->my_model->tambahdata("atur_bahan_ajar", $data)) {
				$this->session->set_flashdata("msg", "<br/><div class='alert alert-info' role='alert'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Bahan Ajar ini berhasil di tambah ke pertemuan ini..!</div>");
			} else {
				$this->session->set_flashdata("msg", "<br/><div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Bahan Ajar ini gagal di tambah ke pertemuan ini..!</div>");
			}
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
	public function detail()
	{
		$IDMAKUL = trim($this->security->xss_clean($IdDos = $this->uri->segment(3)));
		$thnAjar = trim($this->security->xss_clean($IdDos = $this->uri->segment(4)));
		$IDPRODI = trim($this->security->xss_clean($IdDos = $this->uri->segment(5)));
		$NAMAKLS = trim($this->security->xss_clean($IdDos = $this->uri->segment(6)));
		$SEMESTER = trim($this->security->xss_clean($IdDos = $this->uri->segment(7)));
		$where = array('IDDOSEN' => $this->session->userdata('id_user'), 'THSHM' => $thnAjar, 'IDPRODI' => $IDPRODI, 'IDMAKUL' => $IDMAKUL, 'NAMAKLS' => $NAMAKLS, 'SEMESTER' => $SEMESTER);
		$CekMakul = $this->my_model->cek_data("makul_dosen", $where);
		if ($CekMakul->num_rows() >= 1) {
			$thn_ajr = substr($thnAjar, 0, -1); //2017, thnAjar = 20172
			$smt = substr($thnAjar, -1); //2
			if ($smt % 2 != 0) {
				$smt_show = "GANJIL";
			} else {
				$smt_show = "GENAP";
			}
			$data['thnAjar'] = $smt_show . " " . $thn_ajr;
			$data['thnAjarInt'] = $thnAjar;
			$data['data_mk'] = $CekMakul->result();
		} else {
			$this->session->set_flashdata("msg", "<br/><div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Data MK dosen Tidak ditemukan. Silahkan ulangi kembali..!</div>");
		}

		$where = array('IDDOSEN' => $this->session->userdata('id_user'));
		$BahanAjar = $this->my_model->cek_data("bahan_ajar", $where)->result();
		$ArrBahanAjar[''] = '-- PILIH --';
		foreach ($BahanAjar as $row) {
			$ArrBahanAjar[$row->ID] = $row->JUDUL;
		}
		$data['BahanAjar'] = $ArrBahanAjar;

		$where = array('IDMAKUL' => $IDMAKUL, 'KELAS' => $NAMAKLS, 'THNSM' => $thnAjar, 'SEMESTER' => $SEMESTER);
		$CekMhs = $this->my_model->cek_data("mhs_course", $where);
		if ($CekMhs->num_rows() >= 1) {
			$data['GetMhs'] = $CekMhs->result();
		}

		$data['MetodeAjar'] = array("Kelompok" => "Kelompok (Metode belajar kelompok)", "Ceramah" => "Ceramah (Presentasi biasa)", 'Ceramah+' => 'Ceramah + Diskusi', "Studi" => "Studi Kasus", "Diskusi" => "Diskusi Kelompok", "Demontrasi" => "Demontrasi (Demonstration Method)", "Tutorial" => "Tutorial (Tutorial Method)", "PemecahanMasalah" => "Pemecahan Masalah (Problem solving method)", "Percobaan" => "Percobaan (Experimental Method)", "Perancangan" => "Perancangan (Projeck Method)", "Resitasi" => "Resitasi (Recitation Method)", "SesamaTeman" => "Sesama Teman (Peer Teaching Method)");

		if ($this->input->get('pertemuan')) { //Cek input absen Mhs
			$PERTEMUAN = trim($this->security->xss_clean($this->input->get('pertemuan')));


			$cek_mhs = $this->my_model->cek_mhs($IDMAKUL, $thnAjar, $IDPRODI, $NAMAKLS, $SEMESTER, $PERTEMUAN);
			$data['GetAbsenMhs'] = $cek_mhs;

			$cek_mhs_absen = $this->my_model->cek_mhs_absen($IDMAKUL, $thnAjar, $IDPRODI, $NAMAKLS, $SEMESTER, $PERTEMUAN);
			//$data['GetAbsenMhsDetail'] = $cek_mhs_absen;
			if ($cek_mhs_absen != '') {
				foreach ($cek_mhs_absen as $value) {
					$GetAbsenMhsDetail[$value->IDMAHASISWA] = $value->ABSENSI;
				}
				$data['GetAbsenMhsDetail'] = $GetAbsenMhsDetail;
			}

			// $where = array('a.IDMAKUL' => $IDMAKUL, 'a.THNSM' => $thnAjar, 'a.IDPRODI' => $IDPRODI, 'a.KELAS' => $NAMAKLS, 'a.SEMESTER' => $SEMESTER, 'a.PERTEMUAN' => $PERTEMUAN);
			// $this->db->join('mhs_course b', 'a.IDMAHASISWA = b.IDMAHASISWA','left');			
			// $CekAbsenMhs= $this->my_model->cek_data('absen_mhs a', $where);
			// $cek_mhs = $this->my_model->cek_mhs('absen_mhs a', $where);
			// if($CekAbsenMhs->num_rows() >= 1){
			// 	$data['GetAbsenMhs'] = $CekAbsenMhs->result();
			// }

			$data['JlhMhsHadir'] = $data['JlhMhsAbsen'] = 0;
			$where = array('THNSM' => $thnAjar, 'IDPRODI' => $IDPRODI, 'IDMAKUL' => $IDMAKUL, 'KELAS' => $NAMAKLS, 'SEMESTER' => $SEMESTER, 'PERTEMUAN' => $PERTEMUAN, 'ABSENSI' => 'H');
			$CekMhsHadir = $this->my_model->cek_data("absen_mhs", $where);
			if ($CekMhsHadir->num_rows() >= 1) {
				$data['JlhMhsHadir'] = $CekMhsHadir->num_rows();
			}
			$where = array('THNSM' => $thnAjar, 'IDPRODI' => $IDPRODI, 'IDMAKUL' => $IDMAKUL, 'KELAS' => $NAMAKLS, 'SEMESTER' => $SEMESTER, 'PERTEMUAN' => $PERTEMUAN, 'ABSENSI !=' => 'H');
			$CekMhsAbsen = $this->my_model->cek_data("absen_mhs", $where);
			if ($CekMhsAbsen->num_rows() >= 1) {
				$data['JlhMhsAbsen'] = $CekMhsAbsen->num_rows();
			}

			$where = array('IDDOSEN' => $this->session->userdata('id_user'), 'THNSM' => $thnAjar, 'IDPRODI' => $IDPRODI, 'IDMAKUL' => $IDMAKUL, 'KELAS' => $NAMAKLS, 'SEMESTER' => $SEMESTER, 'PERTEMUAN' => $PERTEMUAN);
			$CekAbsenDos = $this->my_model->cek_data("absen_dosen", $where);
			if ($CekAbsenDos->num_rows() >= 1) {
				$data['GetAbsenDos'] = $CekAbsenDos->result();
			}
		}

		$ArrPertemuan[''] = '-- PILIH --';
		for ($a = 1; $a <= 16; $a++) {
			$ArrPertemuan[$a] = "Pertemuan " . $a;
		}
		$data['Pertemuan'] = $ArrPertemuan;

		if ($this->input->get('pertemuan')) {
			$pertemuan = trim($this->security->xss_clean($this->input->get('pertemuan')));
			$where = array('a.IDDOSEN' => $this->session->userdata('id_user'), 'a.THSHM' => $thnAjar, 'a.IDPRODI' => $IDPRODI, 'a.IDMAKUL' => $IDMAKUL, 'a.NAMAKLS' => $NAMAKLS, 'a.SEMESTER' => $SEMESTER, 'a.PERTEMUAN' => $pertemuan);
			$this->db->join('bahan_ajar b', 'b.ID = a.MATERI', 'join');
			$GetBahanAjar = $this->my_model->cek_data("atur_bahan_ajar a", $where);
			if ($GetBahanAjar->num_rows() >= 1) {
				$data['GetBahanAjar'] = $GetBahanAjar->result();
			}
		}
		$data['header'] = "header/header2";
		$data['navbar'] = "navbar/navbar2";
		$data['sidebar'] = "sidebar/sidebar2";
		$data['body'] = "body/view_makul_detail2";
		$data['footer'] = "footer/footer2";
		$this->load->view('template', $data);
		//$this->load->view('body/view_makul_detail', $data);
	}

	public function view()
	{
		$thnAjar = trim($this->security->xss_clean($IdDos = $this->uri->segment(3)));
		$where = array('IDDOSEN' => $this->session->userdata('id_user'), 'THSHM' => $thnAjar);
		$this->db->order_by('NAMAMK', 'ASC');
		$CekMakul = $this->my_model->cek_data("makul_dosen", $where);
		if ($CekMakul->num_rows() >= 1) {
			$thn_ajr = substr($thnAjar, 0, -1);
			$smt = substr($thnAjar, -1);
			if ($smt % 2 != 0) {
				$smt_show = "GANJIL";
			} else {
				$smt_show = "GENAP";
			}
			$data['thnAjarInt'] = $thnAjar;
			$data['thnAjar'] = $smt_show . " " . $thn_ajr;
			$data['data_mk'] = $CekMakul->result();
		} else {
			$this->session->set_flashdata("msg", "<br/><div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Data MK dosen Tidak ditemukan. Silahkan Klik Tombol Sinkronisasi!</div>");
		}
		$data['header'] = "header/header2";
		$data['navbar'] = "navbar/navbar2";
		$data['sidebar'] = "sidebar/sidebar2";
		$data['body'] = "body/view_makul_thsm2";
		$data['footer'] = "footer/footer2";
		$this->load->view('template', $data);
	}

	public function terkini()
	{
		$this->db->select('THSHM');
		$this->db->group_by('THSHM');
		$this->db->order_by('THSHM', 'DESC');
		$this->db->limit(1);
		$where = array('IDDOSEN' => $this->session->userdata('id_user'));
		$LasThnAjar = $this->my_model->cek_data("makul_dosen", $where);
		if ($LasThnAjar->num_rows() >= 1) {
			$showLasThnAjar = $LasThnAjar->row();
			$where = array('IDDOSEN' => $this->session->userdata('id_user'), 'THSHM' => $showLasThnAjar->THSHM);
			$this->db->order_by('NAMAMK', 'ASC');
			$CekMakul = $this->my_model->cek_data("makul_dosen", $where);
			if ($CekMakul->num_rows() >= 1) {
				$thn_ajr = substr($showLasThnAjar->THSHM, 0, -1);
				$smt = substr($showLasThnAjar->THSHM, -1);
				if ($smt % 2 != 0) {
					$smt_show = "GANJIL";
				} else {
					$smt_show = "GENAP";
				}
				$data['thnAjarInt'] = $showLasThnAjar->THSHM;
				$data['thnAjar'] = $smt_show . " " . $thn_ajr;
				$data['data_mk'] = $CekMakul->result();
			} else {
				$this->session->set_flashdata("msg", "<br/><div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Data MK dosen Tidak ditemukan. Silahkan Klik Tombol Sinkronisasi!</div>");
			}
		}
		$data['header'] = "header/header";
		$data['navbar'] = "navbar/navbar";
		$data['sidebar'] = "sidebar/sidebar";
		$data['body'] = "body/view_makul_terkini";
		$data['footer'] = "footer/footer";
		$this->load->view('template', $data);
	}

	public function laporan()
	{
		$IDMAKUL = trim($this->security->xss_clean($IdDos = $this->uri->segment(3)));
		$thnAjar = trim($this->security->xss_clean($IdDos = $this->uri->segment(4)));
		$IDPRODI = trim($this->security->xss_clean($IdDos = $this->uri->segment(5)));
		$NAMAKLS = trim($this->security->xss_clean($IdDos = $this->uri->segment(6)));
		$SEMESTER = trim($this->security->xss_clean($IdDos = $this->uri->segment(7)));
		$where = array('IDDOSEN' => $this->session->userdata('id_user'), 'THSHM' => $thnAjar, 'IDPRODI' => $IDPRODI, 'IDMAKUL' => $IDMAKUL, 'NAMAKLS' => $NAMAKLS, 'SEMESTER' => $SEMESTER);
		$CekMakul = $this->my_model->cek_data("makul_dosen", $where);
		if ($CekMakul->num_rows() >= 1) {
			$thn_ajr = substr($thnAjar, 0, -1);
			$smt = substr($thnAjar, -1);
			if ($smt % 2 != 0) {
				$smt_show = "GANJIL";
			} else {
				$smt_show = "GENAP";
			}
			$data['thnAjar'] = $smt_show . " " . $thn_ajr;
			$data['thnAjarInt'] = $thnAjar;
			//print_r($CekMakul->result());

			$data['data_mk'] = $CekMakul->result();
		} else {
			$this->session->set_flashdata("msg", "<br/><div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Data MK dosen Tidak ditemukan. Silahkan ulangi kembali..!</div>");
		}

		$data['MetodeAjar'] = array("Kelompok" => "Kelompok (Metode belajar kelompok)", "Ceramah" => "Ceramah (Presentasi biasa)", "Ceramah+" => "Ceramah + Diskusi", "Studi" => "Studi Kasus", "Diskusi" => "Diskusi Kelompok", "Demontrasi" => "Demontrasi (Demonstration Method)", "Tutorial" => "Tutorial (Tutorial Method)", "PemecahanMasalah" => "Pemecahan Masalah (Problem solving method)", "Percobaan" => "Percobaan (Experimental Method)", "Perancangan" => "Perancangan (Projeck Method)", "Resitasi" => "Resitasi (Recitation Method)", "SesamaTeman" => "Sesama Teman (Peer Teaching Method)");

		//diambil dari makul detail
		$where = array('IDDOSEN' => $this->session->userdata('id_user'), 'THNSM' => $thnAjar, 'IDPRODI' => $IDPRODI, 'IDMAKUL' => $IDMAKUL, 'KELAS' => $NAMAKLS, 'SEMESTER' => $SEMESTER);
		$CekAbsenDos = $this->my_model->cek_data("absen_dosen", $where);
		if ($CekAbsenDos->num_rows() >= 1) {
			$data['GetAbsenDos'] = $CekAbsenDos->result();
		}

		$data['header'] = "header/header";
		$data['navbar'] = "navbar/navbar";
		$data['sidebar'] = "sidebar/sidebar";
		$data['body'] = "body/view_laporan_detail";
		$data['footer'] = "footer/footer";
		$this->load->view('template', $data);
	}

	public function index()
	{
		$where = array('IDDOSEN' => $this->session->userdata('id_user'));
		$this->db->select('THSHM, COUNT(THSHM) as jlh');
		$this->db->group_by('THSHM');
		$this->db->order_by('THSHM', 'DESC');
		$CekMakul = $this->my_model->cek_data("makul_dosen", $where);
		if ($CekMakul->num_rows() >= 1) {
			$data['data_mk'] = $CekMakul->result();
		} else {
			$this->session->set_flashdata("msg", "<br/><div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Data MK dosen Tidak ditemukan. Silahkan Klik Tombol Sinkronisasi!</div>");
		}
		$data['header'] = "header/header";
		$data['navbar'] = "navbar/navbar";
		$data['sidebar'] = "sidebar/sidebar";
		$data['body'] = "body/dashboard";
		$data['footer'] = "footer/footer";
		$this->load->view('template', $data);
	}
}
 