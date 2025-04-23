<?php
namespace app\controllers;

use app\models\Review;

class ReviewController
{
    public function getReviews(): void
    {
        error_log("ReviewController::getReviews called");
        
        header('Content-Type: application/json; charset=utf-8');
        
        $destination = $_GET['destination'] ?? '';
        error_log("GetReviews - Requested destination: " . $destination);
        
        if (!$destination) {
            error_log("GetReviews - No destination provided");
            http_response_code(400);
            echo json_encode(['error' => 'Destination parameter is required']);
            return;
        }
        
        $validDestinations = ['japan', 'turkey', 'montenegro'];
        if (!in_array($destination, $validDestinations)) {
            error_log("GetReviews - Invalid destination: " . $destination);
            http_response_code(400);
            echo json_encode(['error' => 'Invalid destination']);
            return;
        }
        
        try {
            error_log("GetReviews - Fetching reviews for destination: " . $destination);
            $reviews = Review::forDestination($destination);
            error_log("GetReviews - Found " . count($reviews) . " reviews");
            echo json_encode($reviews);
        } catch (\Exception $e) {
            error_log("Exception in ReviewController::getReviews: " . $e->getMessage());
            error_log("Exception trace: " . $e->getTraceAsString());
            http_response_code(500);
            echo json_encode(['error' => 'Failed to load reviews: ' . $e->getMessage()]);
        }
    }

    public function postReview(): void
    {
        error_log("ReviewController::postReview called");
        
        header('Content-Type: application/json; charset=utf-8');
        
        $rawInput = file_get_contents('php://input');
        error_log("PostReview - Raw input: " . $rawInput);
        
        $data = json_decode($rawInput, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("PostReview - JSON decode error: " . json_last_error_msg());
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON data: ' . json_last_error_msg()]);
            return;
        }
        
        error_log("PostReview - Parsed data: " . print_r($data, true));
        
        if (empty($data['destination']) || 
            empty($data['reviewer_name']) || 
            empty($data['comment']) || 
            !isset($data['stars'])) {
            error_log("PostReview - Missing required fields");
            error_log("destination: " . ($data['destination'] ?? 'missing'));
            error_log("reviewer_name: " . ($data['reviewer_name'] ?? 'missing'));
            error_log("comment: " . ($data['comment'] ?? 'missing'));
            error_log("stars: " . (isset($data['stars']) ? $data['stars'] : 'missing'));
            
            http_response_code(422);
            echo json_encode(['error' => 'All fields are required']);
            return;
        }
        
        $validDestinations = ['japan', 'turkey', 'montenegro'];
        if (!in_array($data['destination'], $validDestinations)) {
            error_log("PostReview - Invalid destination: " . $data['destination']);
            http_response_code(400);
            echo json_encode(['error' => 'Invalid destination']);
            return;
        }
        
        $stars = (int)$data['stars'];
        if ($stars < 1 || $stars > 5) {
            error_log("PostReview - Invalid stars rating: " . $stars);
            http_response_code(422);
            echo json_encode(['error' => 'Rating must be between 1 and 5 stars']);
            return;
        }
        
        try {
            error_log("PostReview - Attempting to save review");
            error_log("Destination: " . $data['destination']);
            error_log("Name: " . $data['reviewer_name']);
            error_log("Comment: " . $data['comment']);
            error_log("Stars: " . $stars);
            
            $review = Review::add(
                $data['destination'],
                $data['reviewer_name'],
                $data['comment'],
                $stars
            );
            
            error_log("PostReview - Review saved successfully. ID: " . $review->id);
            
            echo json_encode($review);
        } catch (\Exception $e) {
            error_log("Exception in ReviewController::postReview: " . $e->getMessage());
            error_log("Exception trace: " . $e->getTraceAsString());
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save review: ' . $e->getMessage()]);
        }
    }
}