<?php

class JobsController extends AppController{
	public $name = 'Jobs';

	/*
	 * Default Index Method
	 */
	public function index(){

		// set category query options
		$options = array(
			'order' => array('Category.name' => 'asc')
		);

		// get categories
		$categories = $this->Job->Category->find('all', $options);

		// set categories
		$this->set('categories', $categories);

		// set query options
		$options = array(
			'order' => array('Job.created' => 'desc'),
			'limit' => 5
			);
		// get job info
		$jobs = $this->Job->find('all', $options);

		// set title
		$this->set('title_for_layout', 'jobsBoard | welcome');

		$this->set('jobs', $jobs);
	}

	/*
	 * Browse Method
	 */
	public function browse($category = null){
		// init conditions array
		$conditions[] = array();

		// check keyword filter
		if($this->request->is('post')){
			if(!empty($this->request->data('keywords'))){
				//die($this->request->data('keywords'));
				$conditions[] = array('OR' => array(
						'Job.title LIKE' => "%" . $this->request->data('keywords') . "%",
						'Job.description LIKE' => "%" . $this->request->data('keywords') . "%"
					)
				);
			}
		}

		// state filter
		if(!empty($this->request->data('states')) && $this->request->data('states') != 'select state'){
			// match state
			$conditions[] = array('Job.state LIKE' => "%" . $this->request->data('states') . "%");
		}

		// category filter
		if(!empty($this->request->data('categories')) && $this->request->data('categories') != 'select category'){
			// match state
			$conditions[] = array('Job.category LIKE' => "%" . $this->request->data('categories') . "%");
		}

		// set category query options
		$options = array(
			'order' => array('Category.name' => 'asc')
		);

		// get categories
		$categories = $this->Job->Category->find('all', $options);

		// set categories
		$this->set('categories', $categories);

		if($category != null){
			// match category
			$conditions[] = array(
				'Job.category_id LIKE' => "%" . $category . "%"
			);
		}

		// set query options
		$options = array(
			'order' => array('Job.created' => 'desc'),
			'conditions' => $conditions,
			'limit' => 8
		);

		// get job info
		$jobs = $this->Job->find('all', $options);

		// set title
		$this->set('title_for_layout', 'jobsBoard | browse for a job');

		$this->set('jobs', $jobs);
	}



	/*
	 * View single jobs
	 */
	public function view( $id ){

		if(!$id){
			throw new NotFoundException(__('Invalid job listing'));
		}

		$job = $this->Job->findById($id);

		if(!$job){
			throw new NotFoundException(__('Invalid job listing'));
		}

		// set title
		$this->set('title_for_layout', $job['Job']['title']);

		$this->set('job', $job);
	}




	/*
	 * Add job
	 */
	public function add(){

		// get categories for select list
		$options = array('order' => array('Category.name' => 'asc'));
		// get categories
		$categories = $this->Job->Category->find('list', $options);
		// set categories
		$this->set('categories', $categories);

		// get types for select list
		$types = $this->Job->Type->find('list');
		// set types
		$this->set('types', $types);


		if($this->request->is('post')){
			$this->Job->create();

			// save logged user_id
			$this->request->data['Job']['user_id'] = $this->Auth->user('id');

			if($this->Job->save($this->request->data)){
				$this->Session->setFlash(__('your job has been listed'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('unable to add your job'));
			}
		}
	}





	/*
	 * Edit job
	 */
	public function edit($id){

		// get categories for select list
		$options = array('order' => array('Category.name' => 'asc'));
		// get categories
		$categories = $this->Job->Category->find('list', $options);
		// set categories
		$this->set('categories', $categories);

		// get types for select list
		$types = $this->Job->Type->find('list');
		// set types
		$this->set('types', $types);

		if(!$id){
			throw new NotFoundException(__('invalid job listing'));
		}

		$job = $this->Job->findById($id);

		if(!$job){
			throw new NotFoundException(__('invalid job listing'));
		}


		if($this->request->is(array('job', 'put'))){
			$this->Job->id = $id;

			// save logged user_id
			//$this->request->data['Job']['user_id'] = 1;

			if($this->Job->save($this->request->data)){
				$this->Session->setFlash(__('your job has been updated'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('unable to update your job'));
			}
		}

		if(!$this->request->data){
			$this->request->data = $job;
		}
	}





	/*
	 * delete job
	 */
	public function delete($id){

		if($this->request->is('get')){
			throw new MethodNotAllowedException();
		}

		if($this->Job->delete($id)){
			$this->Session->SetFlash(__('the job with id: %s has been deleted.', h($id)));
			return $this->redirect(array('action' => 'index'));
		}
	}

}