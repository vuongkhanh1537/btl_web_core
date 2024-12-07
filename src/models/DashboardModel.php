<?php
class DashboardModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getTotalRevenue($startDate = null, $endDate = null) {
        $query = "SELECT SUM(total_payment) as total_revenue FROM order_ WHERE payment_status = 'Completed'";
        if ($startDate && $endDate) {
            $query .= " AND order_time BETWEEN :start_date AND :end_date";
        }
        
        $stmt = $this->conn->prepare($query);
        if ($startDate && $endDate) {
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTopSellingProducts($startDate = null, $endDate = null, $limit = 10) {
        $query = "SELECT p.*, SUM(c.quantity) as sales_count 
                 FROM product p 
                 JOIN contain c ON p.product_id = c.product_id 
                 JOIN order_ o ON c.order_id = o.order_id 
                 WHERE o.payment_status = 'Completed'";
        
        if ($startDate && $endDate) {
            $query .= " AND o.order_time BETWEEN :start_date AND :end_date";
        }
        
        $query .= " GROUP BY p.product_id ORDER BY sales_count DESC LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        if ($startDate && $endDate) {
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
        }
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSalesByCategory($startDate = null, $endDate = null) {
        $query = "SELECT p.category, SUM(c.quantity * c.price) as revenue 
                 FROM product p 
                 JOIN contain c ON p.product_id = c.product_id 
                 JOIN order_ o ON c.order_id = o.order_id 
                 WHERE o.payment_status = 'Completed'";
        
        if ($startDate && $endDate) {
            $query .= " AND o.order_time BETWEEN :start_date AND :end_date";
        }
        
        $query .= " GROUP BY p.category";
        
        $stmt = $this->conn->prepare($query);
        if ($startDate && $endDate) {
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalOrderCount($startDate = null, $endDate = null) {
        $query = "SELECT COUNT(*) as total_order_count FROM order_ WHERE payment_status = 'Completed'";
        if ($startDate && $endDate) {
            $query .= " AND order_time BETWEEN :start_date AND :end_date";
        }
        
        $stmt = $this->conn->prepare($query);
        if ($startDate && $endDate) {
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
