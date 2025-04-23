<?php
namespace app\controllers;

use app\models\Contact;
use app\models\Newsletter;

class ContactController extends Controller
{
    public function contactView(): void
    {
        $this->view('main/contact');
    }

    public function postContact(): void
    {
        error_log("ContactController::postContact called");
        
        header('Content-Type: application/json; charset=utf-8');
        
        $rawInput = file_get_contents('php://input');
        error_log("Contact raw input: " . $rawInput);
        
        $data = json_decode($rawInput, true);
        
        if (!$data) {
            error_log("Failed to parse JSON data: " . json_last_error_msg());
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON data']);
            return;
        }
        
        error_log("Contact data: " . print_r($data, true));
        
        $errors = [];
        
        if (empty($data['name'])) {
            $errors['name'] = 'Name is required';
        }
        
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email is required';
        }
        
        if (empty($data['message'])) {
            $errors['message'] = 'Message is required';
        }
        
        if (!empty($errors)) {
            error_log("Validation errors: " . print_r($errors, true));
            http_response_code(422);
            echo json_encode(['errors' => $errors]);
            return;
        }
        
        try {
            error_log("Attempting to save contact message from: " . $data['name']);
            $contact = Contact::add(
                $data['name'],
                $data['email'],
                $data['message'],
                $data['subject'] ?? ''
            );
            
            error_log("Contact saved successfully, ID: " . $contact->id);
            echo json_encode(['success' => true, 'message' => 'Contact message sent successfully']);
        } catch (\Exception $e) {
            error_log("Exception in postContact: " . $e->getMessage());
            error_log("Exception trace: " . $e->getTraceAsString());
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save contact message']);
        }
    }
    
    public function postNewsletter(): void
    {
        error_log("ContactController::postNewsletter called");
        
        header('Content-Type: application/json; charset=utf-8');
        
        $rawInput = file_get_contents('php://input');
        error_log("Newsletter raw input: " . $rawInput);
        
        $data = json_decode($rawInput, true);
        
        if (!$data) {
            error_log("Failed to parse JSON data: " . json_last_error_msg());
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON data']);
            return;
        }
        
        error_log("Newsletter data: " . print_r($data, true));
        
        if (empty($data['email'])) {
            error_log("Missing email");
            http_response_code(422);
            echo json_encode(['error' => 'Email is required']);
            return;
        }
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            error_log("Invalid email format: " . $data['email']);
            http_response_code(422);
            echo json_encode(['error' => 'Valid email is required']);
            return;
        }
        
        try {
            error_log("Attempting to subscribe email: " . $data['email']);
            $newsletter = Newsletter::subscribe(
                $data['email'],
                $data['name'] ?? ''
            );
            
            error_log("Subscription successful");
            echo json_encode(['success' => true, 'message' => 'Successfully subscribed to newsletter']);
        } catch (\Exception $e) {
            error_log("Exception in postNewsletter: " . $e->getMessage());
            error_log("Exception trace: " . $e->getTraceAsString());
            http_response_code(500);
            echo json_encode(['error' => 'Failed to subscribe to newsletter: ' . $e->getMessage()]);
        }
    }
}