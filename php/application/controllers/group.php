<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once(APPPATH."core/TT_Controller.php");

class Group extends TT_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->model('group_model');
		$this->load->model('user_model');
		$this->load->model('grouprelation_model');
	}

	public function index()
	{
		$this->load->view('base/header');
		$this->load->view('base/group');
		$this->load->view('base/footer');
	}

	public function all()
	{
		$perpage = 10000;
		$groups = $this->group_model->getList(array('status'=>1), '*', 0, $perpage);
		$data = array();
		foreach ($groups as $key => $value) {
			if($groups[$key]['avatar']){
				$groups[$key]['avatar_value'] = $this->config->config['msfs_url'].$groups[$key]['avatar'];
			}
		}
		$result = array(
			'groups'=>$groups,
		);
		echo json_encode($result);
	}

	public function del()
	{
		$id = $this->input->post('id');
		$result = $this->group_model->updateByWhere(array('status'=>0), 'id', $id);
		if($result){
			echo 'success';
		}
	}

	public function add()
	{
		$params = array(
			'name'=>$this->input->post('name'),
			'avatar'=>'',
			'creator'=>1,
			'type'=>1,
			'userCnt'=>0,
			'status'=>1,
			'created'=>time(),
			'updated'=>time()
		);
		$result = $this->group_model->insert($params);
		if($result){
			echo 'success';
		}
	}

	public function edit()
	{
		$params = array(
			'name'=>$this->input->post('name'),
			'avatar'=>$this->input->post('avatar'),
			'adesc'=>$this->input->post('adesc'),
			'createUserId'=>1,
			'type'=>1,
			'userCnt'=>0,
			'status'=>1,
			'updated'=>time()
		);
		$id = $this->input->post('id');
		$result = $this->group_model->updateByWhere($params, 'id', $id);
		if($result){
			echo 'success';
		}
	}

	public function get()
	{
		$id = $this->input->post('id');
		$result = $this->group_model->getOne(array('id'=>$id));
		if($result){
			echo json_encode($result);
		}
	}

	public function getMember()
	{
		$id = $this->input->post('id');
		$perpage = 10000;
		$users = $this->grouprelation_model->getList(array('status'=>1,'id'=>$id), '*', 0, $perpage);
		foreach ($users as $key => $value) {
			$_data = $this->user_model->getOne(array('id'=>$value['userId']));
			$users[$key]['uname'] = $_data['uname'];
		}
		$data = array();
		$result = array(
			'users'=>$users,
		);
		echo json_encode($result);
	}

	public function changeMember()
	{
		$id = $this->input->post('id');
		$userId = $this->input->post('userId');
		$count = $this->input->post('count');
		$change = $this->input->post('change');
		$relation = array(
			'id'=>$id,
			'userId'=>$userId,
			'type'=>1,
			'status'=>1
		);
		if($change){
			$this->grouprelation_model->insert($relation);
		}else{
			$_relation = $this->grouprelation_model->getOne($relation);
			$this->grouprelation_model->update(array('status'=>'0'),$_relation['id']);
		}
		$this->group_model->updateByWhere(array('userCnt'=>$count), 'id', $id);
	}

}