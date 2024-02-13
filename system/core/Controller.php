<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2019, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Application Controller Class
 *
 * This class object is the super class that every library in
 * CodeIgniter will be assigned to.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/general/controllers.html
 */
class CI_Controller {

	/**
	 * Reference to the CI singleton
	 *
	 * @var	object
	 */
	private static $instance;

	/**
	 * CI_Loader
	 *
	 * @var	CI_Loader
	 */
	public $load;

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
    date_default_timezone_set('Asia/Jakarta');
		self::$instance =& $this;

		// Assign all the class objects that were instantiated by the
		// bootstrap file (CodeIgniter.php) to local class variables
		// so that CI can run as one big super object.
		foreach (is_loaded() as $var => $class)
		{
			$this->$var =& load_class($class);
		}

		$this->load =& load_class('Loader', 'core');
		$this->load->initialize();
		log_message('info', 'Controller Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Get the CI singleton
	 *
	 * @static
	 * @return	object
	 */
	public static function &get_instance()
	{
		return self::$instance;
	}
  
  public function splitDate($StringDate) {
    $splitString = explode('/', $StringDate);
    return $splitString[1] . '/' . $splitString[0] . '/' . $splitString[2];
  }

  public function splitDateMYD($StringDate) {
    $splitString = explode('/', $StringDate);
    return $splitString[0] . '/' . $splitString[1] . '/' . $splitString[2];
  }

  public function splitDateAddYears($StringDate, $years) {
    $splitString = explode('/', $StringDate);
    return $splitString[1] . '/' . $splitString[0] . '/' . ($splitString[2] + $years);
  }

}

class UserAccess extends CI_Controller {

  /** Constructor */
  function UserAccess() {
    parent::__construct();
    $this->load->model('UserModel', '', true);
    $this->load->model('UserAccessModel', '', TRUE);
    $this->load->model('MasterModel', '', TRUE);
  }
  
  //Fungsi untuk mendapatkan generate kode transaksi
  public function getCode($Table, $Column, $CodeTransaction) {
    $columnMaster = $this->MasterModel->setColumnNext();
    
    $tmp = date('m');
    $MM = strlen($tmp)<2?('0'.$tmp):$tmp;
    
    $tmp = date('y');
    $YY = substr( $tmp, -2);
    
    $SQL = 'SELECT ' . $Column . ' AS ' . $columnMaster['Row'] . ' ' .
           'FROM ' . $Table . ' ' .
           'WHERE ' . $Column . ' LIKE ? ' .
           'ORDER BY ' . $Column . ' DESC ' .
           'LIMIT 1';
    $getInfo = $this->MasterModel->getQuery($SQL, array($CodeTransaction . '-' . $YY . $MM . '%'))->row();
    
    if($getInfo) {
      $Code = $getInfo->$columnMaster['Row'];
      
      $tmp = substr($Code, 8) + 1;
      $Code = $CodeTransaction . '-' . $YY . $MM .
              ($tmp<10?'00000'.$tmp:
                ($tmp<100?'0000'.$tmp:
                  ($tmp<1000?'000'.$tmp:
                    ($tmp<10000?'00'.$tmp:
                      ($tmp<100000?'0'.$tmp:$tmp)))));
    }
    else {
      $Code = $CodeTransaction . '-' . $YY . $MM . '000001';
    }
    
    return $Code;
  }
  
  /**
   * Show the AdminUser page if already logged or
   * back to the Login page when not logged.
   */
  public function getAccess($nameClass, $Access, $lookup) {
    $columnUserType = $this->UserModel->setColumn();
    $columnUserAccess = $this->UserAccessModel->setColumn();
    
    //Cek hak akses user login
    if ($this->session->userdata('Login') == TRUE) {
      $SQL = "SELECT         UserType.UserType, 
      UserAccess.SubMenu, 
      UserAccess.List_, 
      UserAccess.Info_, 
      UserAccess.Create_, 
      UserAccess.Update_, 
      UserAccess.Delete_,
      UserAccess.Email_	
      FROM UserType 
      LEFT JOIN UserAccess 
      ON UserAccess.ID = UserType.ID WHERE UserType.UserType = '".$this->session->userdata('LoginLevel')."' AND UserAccess.SubMenu = '".$nameClass."'";
      
      // $getInfo = $this->MasterModel->getQuery($SQL, array($this->session->userdata('LoginName'), $nameClass))->row();
      $getInfo = $this->db->query($SQL)->row();

      if ((count($getInfo) > 0) and ( $getInfo->$Access == 1)) {
        return true;
      } else {
        //Set value for show
        $data['h2_title'] = 'Error Access';
        $data['main_view'] = 'Admin/ErrorAccess';

        //Load default view
        if (!$lookup) {
          $this->load->view('Admin/Template', $data);
        } else {
          $this->load->view('Admin/ErrorAccess', $data);
        }
        return false;
      }
    } else {
      $url = str_replace(base_url(),'',current_url());
      $url = str_replace('/','~',$url);
      $data['url'] = $url;
      $data['form_action'] = site_url('Login/processLogin/');
      $this->load->view('Login/Login', $data);
      // redirect('login/processLogin/' . str_replace('/', '~', current_url()));
      return false;
    }
  }

  /** Get list all document and certificate for upload process */
  function getUploadPage($URLtmp) {
    redirect(base_url() . str_replace("~", "/", $URLtmp));
  }

  /** Create directory path */
  function createPath($path) {
    if (!is_dir($path)) {
      //mkdir($path, 0777, TRUE);
      mkdir($path, 777, true);
      chmod($path, 0777);
    }
  }

  /** Remove directory path */
  function removePath($path) {
    if ($path <> 'uploads/Empty.jpg') {
      if (is_dir($path) === true) {
        $files = array_diff(scandir($path), array('.', '..'));
        foreach ($files as $file) {
          $this->removePath(realpath($path) . '/' . $file);
        }
        return rmdir($path);
      } else if (is_file($path) === true) {
        return unlink($path);
      }
    }

    return false;
  }

  /**
   * Show the Notification page if already logged or
   * back to the Login page when not logged.
   */
  public function sendNotif($title, $message, $fcms) {
    $api_key = "AAAAUIiMGQk:APA91bH1XZqH6Y_q0JIgxIHtR_Wr6C3_cvDElHgLOp_wLPvH4Jz0VtPSuJjKfTNOYI8yiUVnpZq-LaQM0L28vq-LT9JhptcGj9XX1cLwmJ9CwpDfzgw9xZPW6gPNnQYBkG_0bi0rtHGywaHX5tjT_2rrbCxamzDvYw";
    $url = "https://fcm.googleapis.com/fcm/send";

    $fields = array(
      'registration_ids' => $fcms,
      'data' => array(
        "title" => $title,
        "message" => $message,
        'type' => 'booking',
        'type_id' => 1
      ),
    );

    $request_headers = array('Authorization: key=' . $api_key, 'Content-Type: application/json');
    $ch = curl_init();

    $this->curl->create($url);
    $this->curl->option(CURLOPT_HTTPHEADER, $request_headers);
    $this->curl->post(json_encode($fields));
    $result = $this->curl->execute();
  }
  
  /** Update Email */
  function SendEmail($Email, $Password, $TO, $CC, $BCC, $Name, $Subject, $Message) {
    $config = array('mailtype'  => 'html',
                    'charset'   => 'utf-8',
                    'protocol'  => 'smtp',
                    'smtp_host' => 'mail.happypuppy.id',
                    'smtp_user' => 'noer.rokhman@happypuppy.id',
                    'smtp_pass'   => '60132323abcd!@',
                    'smtp_crypto' => 'ssl',
                    'smtp_port'   => 465,
                    'crlf'    => "\r\n",
                    'newline' => "\r\n");
    
    // Load library email dan konfigurasinya
    $this->load->library('email', $config);
    
    // Email dan nama pengirim
    $this->email->from('no-reply@happypuppy.com', $Name);
    
    // Email penerima
    $this->email->to($TO);
    $this->email->cc($CC);
    $this->email->bcc($BCC);
    
    // Subject email
    $this->email->subject($Subject);
    
    // Isi email
    $this->email->message($Message);
  
    // Tampilkan pesan sukses atau error
    if ($this->email->send()) {
      $this->session->set_flashdata('message', 'Email berhasil dikirim');
      return true;
    } else {
      $this->session->set_flashdata('message', 'Error! email tidak dapat dikirim.');
      return false;
    }
    return false;
  }
  
  function month_($month) {
    $result = '';
    if($month=='DESEMBER') { $result = '12'; }
    else if($month=='NOVEMBER') { $result = '11'; }
    else if($month=='OKTOBER') { $result = '10'; }
    else if($month=='SEPTEMBER') { $result = '09'; }
    else if($month=='AGUSTUS') { $result = '08'; }
    else if($month=='JULI') { $result = '07'; }
    else if($month=='JUNI') { $result = '06'; }
    else if($month=='MEI') { $result = '05'; }
    else if($month=='APRIL') { $result = '04'; }
    else if($month=='MARET') { $result = '03'; }
    else if($month=='FEBRUARI') { $result = '02'; }
    else if($month=='JANUARI') { $result = '01'; }
    
    return $result;
  }
  
  function monthString_($month) {
    $result = '';
    if($month=='12') { $result = 'DESEMBER'; }
    else if($month=='11') { $result = 'NOVEMBER'; }
    else if($month=='10') { $result = 'OKTOBER'; }
    else if($month=='09') { $result = 'SEPTEMBER'; }
    else if($month=='08') { $result = 'AGUSTUS'; }
    else if($month=='07') { $result = 'JULI'; }
    else if($month=='06') { $result = 'JUNI'; }
    else if($month=='05') { $result = 'MEI'; }
    else if($month=='04') { $result = 'APRIL'; }
    else if($month=='03') { $result = 'MARET'; }
    else if($month=='02') { $result = 'FEBRUARI'; }
    else if($month=='01') { $result = 'JANUARI'; }
    
    return $result;
  }

   /**
   * Used for validate the certificate number - min character (callback_check_length_min)
   * @param for word : String
   * @param for min : int
   */
  function check_length_min($input, $min) {
    $length = strlen($input);

    if ($length >= $min) {
      return TRUE;
    } elseif ($length < $min) {
      $this->form_validation->set_message('check_length_min', 'Minimum number of characters is ' . $min);
      return FALSE;
    }
  }

  /**
   * Used for validate the certificate number - min character (callback_check_length_min)
   * @param for word : String
   * @param for min : int
   */
  function check_length($input, $min) {
    $length = strlen($input);

    if ($length == $min) {
      return TRUE;
    } else {
      $this->form_validation->set_message('check_length', 'Harus berjumlah ' . $min . ' karakter.');
      return FALSE;
    }
  }

  /**
   * Used for validate the certificate number - min character (callback_check_length_min)
   * @param for word : String
   * @param for min : int
   */
  function check_length_empty($input, $min) {
    $length = strlen($input);

    if ($length==0 OR $length==$min) {
      return TRUE;
    } else {
      $this->form_validation->set_message('check_length_empty', 'Harus berjumlah ' . $min . ' karakter.');
      return FALSE;
    }
  }

  /**
   * Used for validate the certificate number - max character (callback_check_length_min)
   * @param for word : String
   * @param for max : int
   */
  function check_length_max($input, $max) {
    $length = strlen($input);

    if ($length <= $max) {
      return TRUE;
    } elseif ($length > $max) {
      $this->form_validation->set_message('check_length_max', 'Maximum number of characters is ' . $max);
      return FALSE;
    }
  }

   /**Cek file is exist */
  function file_exists_($URL) {
    $result = 'uploads/Empty.jpg';
    if ($URL <> '' AND file_exists($URL))
      $result = $URL;

    return $result;
  }

   /**
   * Checek for validate the date format (callback_valid_date)
   * @param for word : String
   */

  function valid_date($str) {
    if ($str <> '') {
      if (!preg_match("^(0[1-9]|1[012])/(0[1-9]|1[0-9]|2[0-9]|3[01])/([0-9]{4})$^", $str)) {
        $this->form_validation->set_message('valid_date', 'Format tanggal tidak valid. mm/dd/yyyy');
        return false;
      } else {
        return true;
      }
    }
  }
  
  //Callback
  function PasswordCheck($str) {
    $str = trim($str);
    if($str == '') {
      die("Password not entered");
    }
    elseif(strlen($str) < 8){
      $this->form_validation->set_message('PasswordCheck', 'Panjang kata sandi harus lebih dari 8 karakter.');
      return false;
    }
    elseif(!(preg_match('/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z]).{8,20}$/', $str))) {
      $this->form_validation->set_message('PasswordCheck', 'Kata sandi tidak memenuhi persyaratan. Harus terdapat angka, huruf besar, huruf kecil dan simbol.');
      return false;
    }
    else {
      return true;
    }
  }

  public function splitDateMYD($StringDate) {
    $splitString = explode('/', $StringDate);
    return $splitString[0] . '/' . $splitString[1] . '/' . $splitString[2];
  }
}
