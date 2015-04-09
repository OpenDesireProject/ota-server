<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {

	/**
	 * Construct for this controller.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(array('form_validation', 'security', 'uri'));
	}

	/**
	 * Main API controller
	 */
	public function index()
	{
		if ( $this->uri->segment(1) !== 'api' )
		{
			$this->http_error(400);
			return;
		}

		$input = file_get_contents("php://input");
		if ( $this->security->xss_clean($input, TRUE) !== TRUE )
		{
			$this->http_error(406);
			return;
		}
		elseif ( empty($input) )
		{
			$this->http_error(406);
			return;
		}

		$_POST = json_decode($input, TRUE);

		$updates = array();
		$error = NULL;

		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('method', 'method', 'trim|required|xss_clean');
		$this->form_validation->set_rules('params[device]', 'params[device]', 'trim|required|xss_clean');
		$this->form_validation->set_rules('params[channels]', 'params[channels]', 'required|xss_clean');
		$this->form_validation->set_rules('params[api_level]', 'params[api_level]', 'xss_clean|integer');
		$this->form_validation->set_rules('params[source_incremental]', 'params[source_incremental]', 'trim|xss_clean');

		if ($this->form_validation->run() === TRUE)
		{
			if ($_POST['method'] === "get_all_builds")
			{
				$this->db->where('device', $_POST['params']['device']);
				if (isset($_POST['params']['api_level']) && $_POST['params']['api_level'] !== NULL)
					$this->db->where('api_level', $_POST['params']['api_level']);
				$this->db->order_by('timestamp', 'ASC');
				$query = $this->db->get('updates');
				foreach ($query->result() as $row)
				{
					$updates[] = array(
						'filename' => $row->filename,
						'incremental' => $row->incremental,
						'timestamp' => $row->timestamp,
						'md5sum' => $row->md5sum,
						'channel' => $row->channel,
						'api_level' => $row->api_level,
						'url' => $row->url,
						'changes' => $row->changes
					);
				}
			}
			else
			{
				$this->http_error(405);
				return;
			}
		}
		else
		{
			$error = validation_errors();
		}

		$result = array(
			'id' => null,
			'result' => $updates,
			'error' => $error === NULL ? null : $error
		);

		$this->output->set_content_type('application/json')->set_output(json_encode($result));
	}

	/**
	 * Dummy v1 call
	 */
	public function v1()
	{
		if ( $this->uri->uri_string() === 'api/v1/build/get_delta' )
		{
			$result['errors'] = array(
				'message' => 'Unable to find delta'
			);
			$this->output->set_content_type('application/json')->set_output(json_encode($result));
			return;
		}
		else
		{
			$this->http_error(400);
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

/* End of file api.php */
/* Location: ./application/controllers/api.php */
