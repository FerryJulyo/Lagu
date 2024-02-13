<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

require_once BASEPATH . '../vendor/autoload.php';

class Home extends CI_Controller
{
	var $limit = 25;
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/Home
	 *	- or -
	 * 		http://example.com/index.php/Home/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/Home/<method_name>
	 * @see https://codeigniter.com/userguide3/general/urls.html
	 */
	public function Home()
	{
		parent::__construct();
		$this->load->model('MasterModel');
		$this->load->library('pagination');
		$this->db2 = $this->load->database('db2', TRUE);
	}

	public function index()
	{
		$this->lagu();
	}

	public function lagu($offset = 0)
	{
		// Konfigurasi pagination
		$config['base_url'] = base_url('index.php/Home/lagu');
		$config['total_rows'] = $this->MasterModel->count_data();
		$config['per_page'] = 20;
		$config['uri_segment'] = 3;

		$this->pagination->initialize($config);

		$data['pagination'] = $this->pagination->create_links();
		$data['results'] = $this->MasterModel->get_data($config['per_page'], $offset);
		$data['type'] = 1;

		$this->load->view('home', $data);
	}
	public function duplicate_song($offset = 0)
	{
		// Konfigurasi pagination
		$config['base_url'] = base_url('index.php/Home/duplicate_song');
		$config['total_rows'] = $this->MasterModel->count_data_duplicate();
		$config['per_page'] = 40;
		$config['uri_segment'] = 3;

		$this->pagination->initialize($config);

		$data['pagination'] = $this->pagination->create_links();

		$duplicate = $this->MasterModel->get_data_duplicate($config['per_page'], $offset);
		$data['type'] = 2;
		$data['results'] = $this->unmerge_data($duplicate);
		$this->load->view('home', $data);
	}
	public function miss_song($offset = 0)
	{
		// Konfigurasi pagination
		$config['base_url'] = base_url('index.php/Home/miss_song');
		$config['total_rows'] = $this->MasterModel->count_data_miss();
		$config['per_page'] = 20;
		$config['uri_segment'] = 3;

		$this->pagination->initialize($config);

		$data['pagination'] = $this->pagination->create_links();

		$data['results'] = $this->MasterModel->get_data_miss($config['per_page'], $offset);
		$data['type'] = 3;

		$this->load->view('home', $data);
	}

	function export()
	{
		$data = $this->db2->query("SELECT id,song
        FROM master_song 
        WHERE id NOT IN (SELECT DISTINCT id_file FROM master_file) 
          AND id NOT IN (SELECT id_song FROM lost) ORDER BY id ASC")->result_array();

		$spreadsheet = IOFactory::load('assets/export_template.xlsx');
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle('Lagu Belum');
		for ($x = 0; $x < count($data); $x++) {
			$sheet->setCellValue('A' . ($x + 2), $data[$x]['id']);
			$sheet->setCellValue('B' . ($x + 2), $data[$x]['song']);
			// $sheet->setCellValue('C' . ($x + 2), $data[$x]['py_str']);
			// $sheet->setCellValue('D' . ($x + 2), $data[$x]['len']);
			// $sheet->setCellValue('E' . ($x + 2), $data[$x]['lan']);
			// $sheet->setCellValue('F' . ($x + 2), $data[$x]['sing_id']);
			// $sheet->setCellValue('G' . ($x + 2), $data[$x]['sing2_id']);
			// $sheet->setCellValue('H' . ($x + 2), $data[$x]['video_type']);
			// $sheet->setCellValue('I' . ($x + 2), $data[$x]['volume']);
			// $sheet->setCellValue('J' . ($x + 2), $data[$x]['brightness']);
			// $sheet->setCellValue('K' . ($x + 2), $data[$x]['contrast']);
			// $sheet->setCellValue('L' . ($x + 2), $data[$x]['saturation']);
			// $sheet->setCellValue('M' . ($x + 2), $data[$x]['grade']);
			// $sheet->setCellValue('N' . ($x + 2), $data[$x]['vcd_audio']);
			// $sheet->setCellValue('O' . ($x + 2), $data[$x]['dvd_audio']);
			// $sheet->setCellValue('P' . ($x + 2), $data[$x]['dvd_music']);
			// $sheet->setCellValue('Q' . ($x + 2), $data[$x]['vod_type']);
			// $sheet->setCellValue('R' . ($x + 2), $data[$x]['pro']);
			// $sheet->setCellValue('S' . ($x + 2), $data[$x]['song_date']);
			// $sheet->setCellValue('T' . ($x + 2), $data[$x]['csong']);
			// $sheet->setCellValue('U' . ($x + 2), $data[$x]['label']);
			// $sheet->setCellValue('V' . ($x + 2), $data[$x]['original']);
			// $sheet->setCellValue('W' . ($x + 2), $data[$x]['composer1']);
			// $sheet->setCellValue('X' . ($x + 2), $data[$x]['composer2']);
			// $sheet->setCellValue('Y' . ($x + 2), $data[$x]['genre1']);
			// $sheet->setCellValue('Z' . ($x + 2), $data[$x]['genre2']);
			// $sheet->setCellValue('AA' . ($x + 2), $data[$x]['ffmpeg']);
			// $sheet->setCellValue('AB' . ($x + 2), $data[$x]['format']);
		}

		$jmlh = (count($data) + 1);
		// Memberikan border pada seluruh rentang sel C5 sampai J18
		$style = 'A2:B' . ($jmlh);
		$borders = $sheet->getStyle($style)->getBorders();
		$borders->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

		// Simpan file Excel
		$writer = new Xlsx($spreadsheet);
		$directory = 'assets/';
		$filename = 'Lagu_Belum.xlsx';
		$filePath = $directory . $filename;
		$writer->save($filePath);

		// Set header untuk mengunduh file
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		// header('Cache-Control: max-age=0');

		// Mengirimkan file Excel ke browser
		$writer->save('php://output');
		unlink($filePath);
	}

	function unmerge_data($data)
	{
		$uniqueArray1 = array();
		$uniqueArray2 = array();

		// Iterasi melalui array utama
		foreach ($data as $item) {
			// Cek apakah id sudah ada di $uniqueArray1
			if (!isset($uniqueArray1[$item['id_file']])) {
				$uniqueArray1[$item['id_file']] = array();
			}

			// Cek apakah id sudah ada di $uniqueArray2
			if (!isset($uniqueArray2[$item['id_file']])) {
				$uniqueArray2[$item['id_file']] = array();
			}

			// Tambahkan item ke array yang sesuai
			$uniqueArray1[$item['id_file']][] = $item;

			// Pindahkan setiap item kedua ke array yang kedua
			if (count($uniqueArray1[$item['id_file']]) > 1) {
				$uniqueArray2[$item['id_file']][] = array_pop($uniqueArray1[$item['id_file']]);
			}
		}
		$data['array1'] = $uniqueArray1;
		$data['array2'] = $uniqueArray2;
		return $data;
	}

	public function search_data($offset = 0)
	{

		$this->form_validation->set_rules('keyword', 'keyword', '');
		$this->form_validation->set_rules('NotUse', 'NotUse', 'required');
		// Jika tidak ada keyword dalam POST, coba ambil dari session
		if ($this->form_validation->run() == TRUE) {
			$keyword = $this->input->post('keyword');
		} else {
			$keyword = $this->session->userdata('search_keyword');
		}

		// Simpan keyword ke session
		$this->session->set_userdata('search_keyword', $keyword);

		// Konfigurasi pagination
		$config['base_url'] = base_url('index.php/Home/search_data');
		$config['total_rows'] = $this->MasterModel->count_search_results($keyword);
		$config['per_page'] = 20; // Sesuaikan dengan jumlah data yang ingin ditampilkan per halaman
		$config['uri_segment'] = 3;

		// Tambahkan parameter pencarian ke URL pagination
		$config['suffix'] = '?keyword=' . $keyword;
		$config['first_url'] = $config['base_url'] . $config['suffix'];
		$data['type'] = 1;
		$this->pagination->initialize($config);

		$data['pagination'] = $this->pagination->create_links();
		$data['results'] = $this->MasterModel->search_data_with_limit($keyword, $config['per_page'], $offset);

		// Mengirimkan keyword ke view agar dapat digunakan untuk menampilkan kembali pada form
		$data['keyword'] = $keyword;

		$this->load->view('home', $data);
	}

	function upload()
	{
		$path = $this->input->post('location');
		if ($_FILES['song_file']['size'] > 0) {
			$output_dir = '\\\\192.168.1.10\\' . $path . '\\';
			$fileName = $_FILES["song_file"]["name"];
			move_uploaded_file($_FILES["song_file"]["tmp_name"], $output_dir . $fileName);
		} else {
			echo '<script>';
			echo 'alert("Upload file berhasil!");';
			echo 'window.location.href = "' . base_url('index.php/Home') . '";';  // Mengarahkan ke halaman Home setelah menampilkan alert
			echo '</script>';
		}
		echo '<script>';
		echo 'alert("Upload file berhasil!");';
		echo 'window.location.href = "' . base_url('index.php/Home') . '";';  // Mengarahkan ke halaman Home setelah menampilkan alert
		echo '</script>';
	}


	function getVideo($id)
	{
		$video = $this->db2->query("SELECT metadata FROM master_file WHERE id_file = '$id'")->row()->metadata;
		$data = json_decode($video, true);

		// Access the 'filename' key from the 'format' array
		$filename = $data['format']['filename'];
		$html = "<video width='500px' height='350px' autobuffer='autobuffer' autoplay='autoplay' loop='loop' controls='controls'>
					<source src='$filename' type='video/mpeg; codecs='avc1.42E01E, mpega.40.2'>
				</video>
				<a href='$filename' style='color:white;'>Link Video</a>";
		echo $html;
	}

	function detailVideo($id)
	{
		$data = $this->db2->query("SELECT A.metadata,B.song FROM master_file A LEFT JOIN master_song B ON A.id_file = B.id WHERE A.id_file = '$id'")->row();
		$html = 'Data not found!';
		if (count($data) > 0) {
			$metadata = json_decode($data->metadata, true);
			$html = $metadata;
			$song = $data->song;
			$size = $metadata['format']['size'];
			$format_name = $metadata['format']['format_name'];
			$width = 'Undefined';
			$height = 'Undefined';
			foreach ($metadata['streams'] as $keys) {
				if ($keys['codec_type'] == 'video') {
					$width = $keys['width'] . 'P';
					$height = $keys['height'] . 'P';
				}
			}
			$bit_rate = $metadata['format']['bit_rate'];
			$duration = $metadata['format']['duration'] . ' detik';

			// $html = "
			// <table class='rwd-table'>
			// 	<tr>
			// 		<td style='font-weight:bold;color:#d3d355'>ID</td>
			// 		<td>$id</td>
			// 		<td style='font-weight:bold;color:#d3d355'>Judul lagu</td>
			// 		<td>$song</td>
			// 	</tr>
			// 	<tr>
			// 		<td style='font-weight:bold;color:#d3d355'>Ukuran</td>
			// 		<td>$size</td>
			// 		<td style='font-weight:bold;color:#d3d355'>Bit rate</td>
			// 		<td>$bit_rate</td>
			// 	<tr>
			// 		<td style='font-weight:bold;color:#d3d355'>Durasi</td>
			// 		<td>$duration</td>
			// 		<td style='font-weight:bold;color:#d3d355'>Format</td>
			// 		<td>$format_name</td>
			// 	</tr>
			// 	<tr>
			// 		<td style='font-weight:bold;color:#d3d355'>Width</td>
			// 		<td>$width</td>
			// 		<td style='font-weight:bold;color:#d3d355'>Height</td>
			// 		<td>$height</td>
			// 	</tr>
			// </table>";
		}
		// Set header sebagai JSON
		// header('Content-Type: application/json');

		// Konversi data ke format JSON dan kirimkan
		$html = json_encode($html);
		$html = "
		<textarea rows=20 style='width:100%;'>$html</textarea>";

		echo $html;
	}
}
