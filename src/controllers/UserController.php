<?php
class UserController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new UserModel($db);
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
            $exist_customer=$this->userModel->getUser($data);
            if(count($exist_customer)>=1){
                return Response::json(409, ['error' => 'Username existed']);
            }
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            $this->userModel->createCustomer($data);
            return Response::json(201, ['message' => 'Create customer successfully']);
        } catch (Exception $e) {
            return Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function login() {
        try {
            $data = Request::getBody();
            if (!Validator::validate($data, [
                'username' => 'required|min|max',
                'password' > 'required|min|max'
            ]))  {
                return Response::json(400, ['error' => 'Invalid data']);
            }
    
            $exist_customer=$this->userModel->getUser($data);
            if (password_verify($data['password'],$exist_customer[0]['password_'])){
                $token=[
                    "sub" => $exist_customer[0]['user_id'],
                    "exp" => "exp",
                    "role" => $exist_customer[0]['role_']
                ];
                $token = $this->auth->encode($token);
                return Response::json(200, ['message' => 'Login successfully',
                                     'token'   => $token
                                    ]);
            }
            else{
                return Response::json(401, ['message' => 'Wrong password']);
            }
            return Response::json(404, ['message' => 'Account not exist']);
                                     

            
        } catch (Exception $e) {
            return Response::json(500, ['error' => $e->getMessage()]);
        }
    }
}
