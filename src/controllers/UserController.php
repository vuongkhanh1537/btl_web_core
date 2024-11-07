<?php
class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->auth = new Authorization();
    }



    public function createCustomer() {
        try {
            $data = Request::getBody();
            
            if (!Validator::validate($data, [
                'name' => 'required|max',
                'username' => 'required|min|max',
                'password' > 'required|min|max',
                'gender' => 'required',
                'birthday' => 'required|min|max',
                'email' => 'required|email',
            ])) {
                Response::json(400, ['error' => 'Invalid data']);
                return;
            }
            $exist_customer=$this->userModel->getCustomer($data);
            if(count($exist_customer)>=1){
                Response::json(409, ['error' => 'Username existed']);
                return;
            }
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            if ($this->userModel->createCustomer($data)) {
                Response::json(201, ['message' => 'Create customer successfully']);
            } 
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function loginCustomer() {
        try {
            $data = Request::getBody();
            
            if (!Validator::validate($data, [
                'username' => 'required|min|max',
                'password' > 'required|min|max'
            ]))  {
                Response::json(400, ['error' => 'Invalid data']);
                return;
            }
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            $exist_customer=$this->userModel->loginCustomer($data);

            if (count($exist_customer)==1){
                $token = $this->auth->encode('customer');
                Response::json(200, ['message' => 'Create customer successfully',
                                     'token'   => $token
                                    ]);
            }

            
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function loginManager() {
        $data = Request::getBody();
            
    if (!Validator::validate($data, [
            'username' => 'required|min|max',
            'password' > 'required|min|max'
        ]))  {
            Response::json(400, ['error' => 'Invalid data']);
            return;
        }
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        $exist_manager=$this->userModel->loginManager($data);

        if (count($exist_manager)==1){
            $token = $this->auth->encode('manager');
            Response::json(200, ['message' => 'Create manager successfully',
                                 'token'   => $token
                                ]);
    }

        
    } catch (Exception $e) {
        Response::json(500, ['error' => $e->getMessage()]);
    }
}