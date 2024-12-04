<?php
class DashboardController {
    private $dashboardModel;
    private $auth;

    public function __construct($db) {
        $this->dashboardModel = new DashboardModel($db);
        $this->auth = new Authorization();
    }

    private function getDateRange() {
        $queryParams = Request::getQueryParams();
        return [
            'start_date' => $queryParams['start_date'] ?? null,
            'end_date' => $queryParams['end_date'] ?? null
        ];
    }

    public function getTotalRevenue() {
        try {
            $dateRange = $this->getDateRange();
            $revenue = $this->dashboardModel->getTotalRevenue($dateRange['start_date'], $dateRange['end_date']);
            Response::json(200, ['data' => $revenue]);
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function getTopSellingProducts() {
        try {
            $dateRange = $this->getDateRange();
            $products = $this->dashboardModel->getTopSellingProducts($dateRange['start_date'], $dateRange['end_date']);
            Response::json(200, ['data' => $products]);
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function getSalesByCategory() {
        try {
            $dateRange = $this->getDateRange();
            $sales = $this->dashboardModel->getSalesByCategory($dateRange['start_date'], $dateRange['end_date']);
            Response::json(200, ['data' => $sales]);
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function getTotalOrderCount() {
        try {
            $dateRange = $this->getDateRange();
            $count = $this->dashboardModel->getTotalOrderCount($dateRange['start_date'], $dateRange['end_date']);
            Response::json(200, ['data' => $count]);
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }
}
