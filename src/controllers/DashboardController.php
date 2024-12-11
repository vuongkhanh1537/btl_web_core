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
        
        // Default to last 30 days if no dates provided
        $end = new DateTime();
        $start = (new DateTime())->modify('-30 days');
        
        $startDate = null;
        $endDate = null;
        
        if (!empty($queryParams['start_date'])) {
            try {
                $start = new DateTime($queryParams['start_date']);
                $startDate = $start->format('Y-m-d H:i:s');
            } catch (Exception $e) {
                throw new Exception('Invalid start_date format');
            }
        }
        
        if (!empty($queryParams['end_date'])) {
            try {
                $end = new DateTime($queryParams['end_date']); 
                $endDate = $end->format('Y-m-d H:i:s');
            } catch (Exception $e) {
                throw new Exception('Invalid end_date format');
            }
        }
        
        // Validate date range
        if ($startDate && $endDate && $start > $end) {
            throw new Exception('Start date must be before end date');
        }
    
        return [
            'start_date' => $startDate ?? $start->format('Y-m-d H:i:s'),
            'end_date' => $endDate ?? $end->format('Y-m-d H:i:s')
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
