<?php
include "connection.php";
session_start();

if($_SESSION['user'] != "admin"){
    http_response_code(403);
    echo "Unauthorized";
    exit;
}

if(isset($_POST['id']) && isset($_POST['action'])){
    $id = (int)$_POST['id'];
    $action = $_POST['action'];

    if($action == 'approve') $status = 'Approved';
    elseif($action == 'reject') $status = 'Rejected';
    else { http_response_code(400); echo "Invalid action"; exit; }

    $stmt = $conn->prepare("UPDATE emp_attendance SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    if($stmt->execute()){
        echo $status; // send to JS
    } else {
        http_response_code(500);
        echo "Failed to update leave status";
    }
} else {
    http_response_code(400);
    echo "Invalid request";
}
?>
