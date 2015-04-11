<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dl extends CI_Controller {

	/**
	 * Construct for this controller.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper(array('security', 'url'));
	}

	/**
	 * Main DL controller
	 */
	public function index()
	{
		$this->http_error(400);
		return;
	}

	/**
	 * Redirect ROM downloads
	 */
	public function rom($filename = NULL)
	{
		if ( $filename === NULL )
		{
			$this->http_error(400);
			return;
		}

		$filename = sanitize_filename(xss_clean($filename));

		$this->db->select('url');
		$this->db->where('filename', $filename);
		$this->db->order_by('RAND()');
		$this->db->limit(1);
		$query = $this->db->get('updates');
		if ( $query->num_rows() === 1 )
		{
			redirect($query->result_array()[0]['url']);
			return;
		}
		else
		{
			$this->http_error(404);
			return;
		}
	}

	/**
	 * Redirect changelog downloads
	 */
	public function changes($filename = NULL)
	{
		if ( $filename === NULL )
		{
			$this->http_error(400);
			return;
		}

		$filename = sanitize_filename(xss_clean($filename));

		$this->db->select('changes');
		$this->db->where('filename', $filename);
		$this->db->order_by('RAND()');
		$this->db->limit(1);
		$query = $this->db->get('updates');
		if ( $query->num_rows() === 1 )
		{
			redirect($query->result_array()[0]['changes']);
			return;
		}
		else
		{
			$this->http_error(404);
			return;
		}
	}

	/**
	 * Return HTTP response codes.
	 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
	 * @param int $code http response code
	 */
	private function http_error($code)
	{
		switch ($code)
		{
			case 400:
				$this->output->set_status_header('400');
				echo "400 Bad Request";
				break;
			case 404:
				$this->output->set_status_header('404');
				echo "404 Not Found";
				break;
			case 405:
				$this->output->set_status_header('405');
				echo "405 Method Not Allowed";
				break;
			case 406:
				$this->output->set_status_header('406');
				echo "406 Not Acceptable";
				break;
		}
	}
}

/* End of file dl.php */
/* Location: ./application/controllers/dl.php */
