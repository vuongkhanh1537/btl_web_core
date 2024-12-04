<?php
class CollectionController {
    private $collectionModel;
    private $auth;

    public function __construct($db) {
        $this->collectionModel = new CollectionModel($db);
        $this->auth = new Auth();
    }

    public function index() {
        try {
            //$this->auth->checkPermission('collection', 'read');
            $collections = $this->collectionModel->getAll();
            Response::json(200, $collections);
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function show($id) {
        try {
            $collection = $this->collectionModel->getById($id);
            if ($collection) {
                Response::json(200, $collection);
            } else {
                Response::json(404, ['error' => 'Collection not found']);
            }
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function create() {
        try {
            //$this->auth->checkPermission('collection', 'create');
            $data = Request::getBody();
            
            if ($this->collectionModel->validateAndCreate($data)) {
                Response::json(201, ['message' => 'Collection created successfully']);
            }
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function update($id) {
        try {
            //$this->auth->checkPermission('collection', 'update');
            $data = Request::getBody();
            
            if ($this->collectionModel->validateAndUpdate($id, $data)) {
                Response::json(200, ['message' => 'Collection updated successfully']);
            }
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function delete($id) {
        try {
            //$this->auth->checkPermission('collection', 'delete');
            if ($this->collectionModel->delete($id)) {
                Response::json(200, ['message' => 'Collection deleted successfully']);
            } else {
                Response::json(500, ['error' => 'Failed to delete collection']);
            }
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }
}
