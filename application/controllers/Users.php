<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
 
class Users extends CI_Controller { 
     
    function __construct() { 
        parent::__construct(); 
         
        // Load form validation ibrary & user model 
        $this->load->library('form_validation'); 
        $this->load->model('user'); 
         
        // User login status 
        $this->isUserLoggedIn = $this->session->userdata('isUserLoggedIn'); 
    } 
     
    public function index(){ 
        if($this->isUserLoggedIn){ 
            redirect('users/account'); 
        }else{ 
            redirect('users/login'); 
        } 
    } 
 
    public function account(){ 
        $data = array(); 
        if($this->isUserLoggedIn){ 
            $con = array( 
                'id' => $this->session->userdata('userId') 
            ); 
            $data['user'] = $this->user->getRows($con); 
             
            // Pass the user data and load view 
            $this->load->view('elements/header', $data); 
            $this->load->view('users/account', $data); 
            $this->load->view('elements/footer'); 
        }else{ 
            redirect('users/login'); 
        } 
    } 
 
    public function login(){ 
        $data = array(); 
         
        // Get messages from the session 
        if($this->session->userdata('success_msg')){ 
            $data['success_msg'] = $this->session->userdata('success_msg'); 
            $this->session->unset_userdata('success_msg'); 
        } 
        if($this->session->userdata('error_msg')){ 
            $data['error_msg'] = $this->session->userdata('error_msg'); 
            $this->session->unset_userdata('error_msg'); 
        } 
         
        // If login request submitted 
        if($this->input->post('loginSubmit')){ 
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email'); 
            $this->form_validation->set_rules('password', 'password', 'required'); 
             
            if($this->form_validation->run() == true){ 
                $con = array( 
                    'returnType' => 'single', 
                    'conditions' => array( 
                        'email'=> $this->input->post('email'), 
                        'password' => md5($this->input->post('password')), 
                        'status' => 1 
                    ) 
                ); 
                $checkLogin = $this->user->getRows($con); 
                if($checkLogin){ 
                    $this->session->set_userdata('isUserLoggedIn', TRUE); 
                    $this->session->set_userdata('userId', $checkLogin['id']); 
                    redirect('users/account/'); 
                }else{ 
                    $data['error_msg'] = 'Wrong email or password, please try again.'; 
                } 
            }else{ 
                $data['error_msg'] = 'Please fill all the mandatory fields.'; 
            } 
        } 
         
        // Load view 
        $this->load->view('elements/header', $data); 
        $this->load->view('users/login', $data); 
        $this->load->view('elements/footer'); 
    } 
 
    public function registration(){ 
        $data = $userData = array(); 
         
        // If registration request is submitted 
        if($this->input->post('signupSubmit')){ 
            $this->form_validation->set_rules('first_name', 'First Name', 'required'); 
            $this->form_validation->set_rules('last_name', 'Last Name', 'required'); 
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_email_check'); 
            $this->form_validation->set_rules('password', 'password', 'required'); 
            $this->form_validation->set_rules('conf_password', 'confirm password', 'required|matches[password]'); 
 
            $userData = array( 
                'first_name' => strip_tags($this->input->post('first_name')), 
                'last_name' => strip_tags($this->input->post('last_name')), 
                'email' => strip_tags($this->input->post('email')), 
                'password' => md5($this->input->post('password')), 
                'gender' => $this->input->post('gender'), 
                'phone' => strip_tags($this->input->post('phone')) 
            ); 
 
            if($this->form_validation->run() == true){ 
                $insert = $this->user->insert($userData); 
                if($insert){ 
                    $this->session->set_userdata('success_msg', 'Your account registration has been successful. Please login to your account.'); 
                    redirect('users/login'); 
                }else{ 
                    $data['error_msg'] = 'Some problems occured, please try again.'; 
                } 
            }else{ 
                $data['error_msg'] = 'Please fill all the mandatory fields.'; 
            } 
        } 
         
        // Posted data 
        $data['user'] = $userData; 
         
        // Load view 
        $this->load->view('elements/header', $data); 
        $this->load->view('users/registration', $data); 
        $this->load->view('elements/footer'); 
    } 
     
    public function logout(){ 
        $this->session->unset_userdata('isUserLoggedIn'); 
        $this->session->unset_userdata('userId'); 
        $this->session->sess_destroy(); 
        redirect('users/login/'); 
    } 


	  public function index_page()

   {
      // $this->load->database();
         $search = trim((string) $this->input->get('search', TRUE));
         $gender = $this->input->get('gender', TRUE);
       $page = max(1, (int) $this->input->get('page'));

         $this->apply_user_filters($search, $gender);
         $total = $this->db->count_all_results('users');

         $this->apply_user_filters($search, $gender);
       $this->db->select('id, first_name, last_name, email, gender, phone, created, modified, status');
       $this->db->order_by('id', 'DESC');
       $this->db->limit(5, ($page - 1) * 5);

       $query = $this->db->get("users");

       $data['data'] = $query->result_array();
       foreach ($data['data'] as &$user) {
           $user['password'] = '********';
       }
       unset($user);

       $data['total'] = $total;

       $this->send_json($data);

   }

    private function apply_user_filters($search, $gender)
    {
        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('first_name', $search);
            $this->db->or_like('last_name', $search);
            $this->db->or_like('email', $search);
            $this->db->or_like('phone', $search);
            $this->db->group_end();
        }

        if (in_array($gender, array('Male', 'Female'), TRUE)) {
            $this->db->where('gender', $gender);
        }
    }

    public function create()
    {
        if (!$this->isUserLoggedIn) {
            return $this->send_json(array('message' => 'Please log in to add users.'), 401);
        }

        $firstName = trim(strip_tags((string) $this->input->post('first_name')));
        $lastName = trim(strip_tags((string) $this->input->post('last_name')));
        $email = trim((string) $this->input->post('email'));
        $password = (string) $this->input->post('password');
        $gender = $this->input->post('gender');
        $phone = trim(strip_tags((string) $this->input->post('phone')));
        $status = $this->input->post('status');

        if (!$firstName) {
            return $this->send_json(array('field' => 'first_name', 'message' => 'First name is required.'), 422);
        }
        if (!$lastName) {
            return $this->send_json(array('field' => 'last_name', 'message' => 'Last name is required.'), 422);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->send_json(array('field' => 'email', 'message' => 'Enter a valid email address.'), 422);
        }
        if (strlen($password) < 6) {
            return $this->send_json(array('field' => 'password', 'message' => 'Password must be at least 6 characters.'), 422);
        }
        if (!in_array($gender, array('Male', 'Female'), TRUE)) {
            return $this->send_json(array('field' => 'gender', 'message' => 'Choose a valid gender.'), 422);
        }
        if (!in_array($status, array('0', '1'), TRUE)) {
            return $this->send_json(array('field' => 'status', 'message' => 'Choose a valid status.'), 422);
        }

        if ($this->user->getRows(array(
            'returnType' => 'count',
            'conditions' => array('email' => $email)
        )) > 0) {
            return $this->send_json(array('field' => 'email', 'message' => 'That email address is already in use.'), 409);
        }

        $userId = $this->user->insert(array(
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => md5($password),
            'gender' => $gender,
            'phone' => $phone,
            'status' => $status
        ));

        if (!$userId) {
            return $this->send_json(array('message' => 'Unable to add the user.'), 500);
        }

        $this->send_json(array('message' => 'User added.', 'id' => $userId));
    }

    public function update($id = 0)
    {
        if (!$this->isUserLoggedIn) {
            return $this->send_json(array('message' => 'Please log in to edit users.'), 401);
        }

        $id = (int) $id;
        $firstName = trim(strip_tags((string) $this->input->post('first_name')));
        $lastName = trim(strip_tags((string) $this->input->post('last_name')));
        $email = trim((string) $this->input->post('email'));
        $gender = $this->input->post('gender');
        $phone = trim(strip_tags((string) $this->input->post('phone')));
        $status = $this->input->post('status');

        if (!$id || !$firstName || !$lastName || !filter_var($email, FILTER_VALIDATE_EMAIL)
            || !in_array($gender, array('Male', 'Female'), TRUE)
            || !in_array($status, array('0', '1'), TRUE)) {
            return $this->send_json(array('message' => 'Please provide valid user details.'), 422);
        }

        if (!$this->db->get_where('users', array('id' => $id))->row_array()) {
            return $this->send_json(array('message' => 'User not found.'), 404);
        }

        $this->db->where('email', $email);
        $this->db->where('id !=', $id);
        if ($this->db->count_all_results('users') > 0) {
            return $this->send_json(array('message' => 'That email address is already in use.'), 409);
        }

        $updateData = array(
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'gender' => $gender,
            'phone' => $phone,
            'status' => $status,
            'modified' => date('Y-m-d H:i:s')
        );
        $password = (string) $this->input->post('password');
        if ($password !== '') {
            $updateData['password'] = md5($password);
        }

        $this->db->where('id', $id);
        if (!$this->db->update('users', $updateData)) {
            return $this->send_json(array('message' => 'Unable to update the user.'), 500);
        }

        $this->send_json(array('message' => 'User updated.'));
    }

    public function delete($id = 0)
    {
        if (!$this->isUserLoggedIn) {
            return $this->send_json(array('message' => 'Please log in to delete users.'), 401);
        }

        $id = (int) $id;
        if (!$id || !$this->db->get_where('users', array('id' => $id))->row_array()) {
            return $this->send_json(array('message' => 'User not found.'), 404);
        }

        $this->db->where('id', $id);
        if (!$this->db->delete('users')) {
            return $this->send_json(array('message' => 'Unable to delete the user.'), 500);
        }

        $this->send_json(array('message' => 'User deleted.'));
    }

    private function send_json($data, $status = 200)
    {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($data));
    }
     
     
    // Existing email check during validation 
    public function email_check($str){ 
        $con = array( 
            'returnType' => 'count', 
            'conditions' => array( 
                'email' => $str 
            ) 
        ); 
        $checkEmail = $this->user->getRows($con); 
        if($checkEmail > 0){ 
            $this->form_validation->set_message('email_check', 'The given email already exists.'); 
            return FALSE; 
        }else{ 
            return TRUE; 
        } 
    } 
}
