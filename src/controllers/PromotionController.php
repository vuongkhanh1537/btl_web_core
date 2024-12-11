<?php
class PromotionController {
    private $promotionModel;
    private $auth;

    public function __construct($db) {
        $this->promotionModel = new PromotionModel($db);
        $this->auth = new Authorization();
    }

    public function index() {
        try {
            // $role = $this->auth->getRole();
            // if ($role !== 'admin') {
            //     Response::json(403, ['error' => 'Unauthorized access']);
            //     return;
            // }

            $promotions = $this->promotionModel->getAll();
            Response::json(200, [
                'message' => 'Get all promotions successfully',
                'data' => $promotions
            ]);
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function create() {
        try {
            // $role = $this->auth->getRole();
            // if ($role !== 'admin') {
            //     Response::json(403, ['error' => 'Unauthorized access']);
            //     return;
            // }

            $data = Request::getBody();
            if ($this->promotionModel->validateAndCreate($data)) {
                Response::json(201, [
                    'message' => 'Promotion added successfully'
                ]);
            }
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function update($id) {
        try {
            // $role = $this->auth->getRole();
            // if ($role !== 'admin') {
            //     Response::json(403, ['error' => 'Unauthorized access']);
            //     return;
            // }

            $data = Request::getBody();
            if ($this->promotionModel->validateAndUpdate($id, $data)) {
                Response::json(200, [
                    'message' => 'Promotion updated successfully'
                ]);
            }
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function delete($id) {
        try {
            // $role = $this->auth->getRole();
            // if ($role !== 'admin') {
            //     Response::json(403, ['error' => 'Unauthorized access']);
            //     return;
            // }

            if ($this->promotionModel->delete($id)) {
                Response::json(200, [
                    'message' => 'Promotion deleted successfully'
                ]);
            }
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }
}