<?php
class Response {
    public static function json($statusCode, $data) {
        header("Content-Type: application/json; charset=UTF-8");
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
}