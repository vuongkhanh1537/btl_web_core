<?php
class UserController {
    private $userModel;
    private $auth;

    public function __construct($db) {
        $this->userModel = new UserModel($db);
        $this->auth = new Authorization();
    }

    public function signup() {
        try {
            $data = Request::getBody();
            
            // Hash password before creating
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            
            if ($this->userModel->validateAndCreate($data)) {
                Response::json(201, ['message' => 'Customer created successfully']);
            }
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function login() {
        try {
            $data = Request::getBody();
            
            $user = $this->userModel->validateLogin($data);
            
            $token = $this->auth->encode([
                "sub" => $user['user_id'],
                "exp" => 'exp',
                "role" => $user['role_']
            ]);

            Response::json(200, [
                'message' => 'Login successful',
                'token' => $token
            ]);

        } catch (Exception $e) {
            $code = $e->getMessage() === 'User not found' ? 404 : 401;
            Response::json($code, ['error' => $e->getMessage()]);
        }
    }

    public function index() {
        try {
            $users = $this->userModel->getAll();
            Response::json(200, $users);
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function show($id) {
        try {
            $user = $this->userModel->getById($id);
            Response::json(200, $user);
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function update($id) {
        try {
            $data = Request::getBody();
            $data['user_id'] = $id;
            
            if ($this->userModel->validateAndUpdate($data)) {
                Response::json(200, ['message' => 'User updated successfully']);
            }
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function delete($id) {
        try {
            if ($this->userModel->delete($id)) {
                Response::json(200, ['message' => 'User deleted successfully']);
            }
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

}
