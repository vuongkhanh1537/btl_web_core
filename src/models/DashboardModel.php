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
        // Base query with product details and rating calculation
        $query = "SELECT p.product_id as id, 
                         p.name_ as name,
                         p.price,
                         p.image_path as image,
                         p.category,
                         COALESCE(SUM(c.quantity), 0) as sales_count,
                         COALESCE(SUM(c.quantity * c.price), 0) as revenue,
                         COALESCE(AVG(r.score), 5.0) as rating
                  FROM product p
                  LEFT JOIN contain c ON p.product_id = c.product_id 
                  LEFT JOIN order_ o ON c.order_id = o.order_id AND o.payment_status = 'Completed'
                  LEFT JOIN review r ON p.product_id = r.product_id";
        
        if ($startDate && $endDate) {
            $query .= " WHERE (o.order_time IS NULL OR o.order_time BETWEEN :start_date AND :end_date)";
        }
        
        $query .= " GROUP BY p.product_id, p.name_, p.price, p.image_path, p.category
                    ORDER BY sales_count DESC, rating DESC 
                    LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        
        if ($startDate && $endDate) {
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
        }
        
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($results)) {
            return []; // Return empty array instead of null
        }
        
        // Format numbers
        foreach ($results as &$product) {
            $product['sales_count'] = (int)$product['sales_count'];
            $product['revenue'] = (float)$product['revenue'];
            $product['price'] = (float)$product['price'];
            $product['rating'] = (float)$product['rating'];
        }
        
        return $results;
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
