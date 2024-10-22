<?php
namespace App;
require_once 'db_connection.php';

class Message {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function sendMessage($senderId, $receiverId, $messageText, $machineId = null, $jobId = null) {
        $stmt = $this->conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, machine_id, job_id, sent_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iisii", $senderId, $receiverId, $messageText, $machineId, $jobId);
        return $stmt->execute();
    }

    public function getMessages($userId) {
        $stmt = $this->conn->prepare("SELECT m.*, u.username as sender_name, mc.machine_name, j.job_name 
                                      FROM messages m 
                                      JOIN users u ON m.sender_id = u.id 
                                      LEFT JOIN machines mc ON m.machine_id = mc.id
                                      LEFT JOIN jobs j ON m.job_id = j.job_id
                                      WHERE m.receiver_id = ? 
                                      ORDER BY m.sent_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function markAsRead($messageId, $userId) {
        $stmt = $this->conn->prepare("UPDATE messages SET read_at = NOW() WHERE id = ? AND receiver_id = ? AND read_at IS NULL");
        $stmt->bind_param("ii", $messageId, $userId);
        return $stmt->execute();
    }
}
