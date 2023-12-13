<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {


    case "PUT":
        $status = json_decode(file_get_contents('php://input'));
        $sql = "UPDATE post 
            SET status= :status
            WHERE post_id = :post_id";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':post_id', $status->post_id);
        $stmt->bindParam(':status', $status->status);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "status updated successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "status update failed"
            ];
        }

        break;
}
