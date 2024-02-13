<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

require_once BASEPATH . '../vendor/autoload.php';

class Rename extends CI_Controller
{
	var $limit = 25;
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/Rename
	 *	- or -
	 * 		http://example.com/index.php/Rename/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/Rename/<method_name>
	 * @see https://codeigniter.com/userguide3/general/urls.html
	 */
	public function Rename()
	{
		parent::__construct();
		$this->load->model('MasterModel');
		$this->load->library('pagination');
		$this->db2 = $this->load->database('db2', TRUE);
	}

	public function index()
	{
		$folder = '\\\\192.168.1.10\\tampungan_lagu\\renamed';
		$files = scandir($folder);

		foreach ($files as $file) {
			// Mengabaikan entri . dan ..
			if ($file != '.' && $file != '..') {
				$file_path = $folder . '\\' . $file;
				$file_extension = pathinfo($file_path, PATHINFO_EXTENSION);
				$file_name_without_extension = pathinfo($file_path, PATHINFO_FILENAME);
				$last_character = substr($file_name_without_extension, -1);

				// Menentukan karakter baru berdasarkan aturan yang diberikan
				switch ($last_character) {
					case '1':
						$new_last_character = 'A';
						break;
					case '2':
						$new_last_character = 'B';
						break;
					case '3':
						$new_last_character = 'P';
						break;
					default:
						// Jika karakter terakhir bukan 1, 2, atau 3, maka tidak dilakukan perubahan
						$new_last_character = $last_character;
				}

				// Membuat nama file baru dengan karakter terakhir yang telah diubah
				$new_file_name = substr_replace($file_name_without_extension, $new_last_character, -1) . '.' . $file_extension;
				$new_file_path = $folder . '\\' . $new_file_name;

				// Melakukan rename file
				if (rename($file_path, $new_file_path)) {
					echo "File $file renamed to $new_file_name successfully.<br>";
				} else {
					echo "Failed to rename file $file.<br>";
				}
			}
		}
	}
}
