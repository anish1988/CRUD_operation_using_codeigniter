<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model','user');
	}

	public function index()
	{
		$this->load->helper('url');
		$this->load->view('user_view');
	}

	public function ajax_list()
	{
		$list = $this->user->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $user) {
			$no++;
			$row = array();
			$row[] = $user->name;
			$row[] = $user->country;
			$row[] = $user->email;
			$row[] = $user->mobile_no;
			$row[] = $user->about_you;
			$row[] = $user->birthday;
			//add html for action
			$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void()" title="Edit" onclick="edit_user('."'".$user->id."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
				  <a class="btn btn-sm btn-danger" href="javascript:void()" title="Hapus" onclick="delete_user('."'".$user->id."'".')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->user->count_all(),
						"recordsFiltered" => $this->user->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->user->get_by_id($id);
		$data->birthday = ($data->birthday == '0000-00-00') ? '' : $data->birthday; // if 0000-00-00 set tu empty for datepicker compatibility
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate();
		$data = array(
				'name' => $this->input->post('name'),
                'country' => $this->input->post('country'),
                'email' => $this->input->post('email'),
                'mobile_no' => $this->input->post('mobile_no'),
                'about_you' => $this->input->post('about_you'),
                'birthday' => @date('Y-m-d', @strtotime($this->input->post('birthday')))
			);
		$insert = $this->user->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$this->_validate();
		$data = array(
				'name' => $this->input->post('name'),
                'country' => $this->input->post('country'),
                'email' => $this->input->post('email'),
                'mobile_no' => $this->input->post('mobile_no'),
                'about_you' => $this->input->post('about_you'),
                'birthday' => @date('Y-m-d', @strtotime($this->input->post('birthday')))
			);
		$this->user->update(array('id' => $this->input->post('id')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_delete($id)
	{
		$this->user->delete_by_id($id);
		echo json_encode(array("status" => TRUE));
	}


	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('name') == '')
		{
			$data['inputerror'][] = 'name';
			$data['error_string'][] = 'Name is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('country') == '')
		{
			$data['inputerror'][] = 'country';
			$data['error_string'][] = 'Country name is required';
			$data['status'] = FALSE;
		}
		if($this->input->post('email') == '')
		{
			$data['inputerror'][] = 'email';
			$data['error_string'][] = 'Email is required';
			$data['status'] = FALSE;
		}
		if($this->input->post('mobile_no') == '')
		{
			$data['inputerror'][] = 'mobile_no';
			$data['error_string'][] = 'Mobile No is required';
			$data['status'] = FALSE;
		}
		if($this->input->post('about_you') == '')
		{
			$data['inputerror'][] = 'about_you';
			$data['error_string'][] = 'About you is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('birthday') == '')
		{
			$data['inputerror'][] = 'birthday';
			$data['error_string'][] = 'Date of Birth is required';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

}
