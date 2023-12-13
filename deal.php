<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":

        if (isset($_GET['post_id'])) {
            $post_id = $_GET['post_id'];

            $sql = "SELECT * FROM deal WHERE post_id = :post_id  ORDER BY deal_id DESC";
        }


        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($post_id)) {
                $stmt->bindParam(':post_id', $post_id);
            }


            $stmt->execute();
            $bid = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($bid);
        }


        break;

    case "POST":
        $bid = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO deal (deal_name, post_id, bidding_id, deal_total_price, created_at) VALUES (:deal_name, :post_id, :bidding_id, :deal_total_price, :created_at)";
        $stmt = $conn->prepare($sql);

        $created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(':bidding_id', $bid->bidding_id);
        $stmt->bindParam(':deal_name', $bid->deal_name);
        $stmt->bindParam(':post_id', $bid->post_id);
        $stmt->bindParam(':deal_total_price',  $bid->deal_total_price);
        $stmt->bindParam(':created_at',  $created_at);


        if ($stmt->execute()) {

            $sql2 = "UPDATE post 
            SET status= :status
            WHERE post_id = :post_id";

            $stmt3 = $conn->prepare($sql2);

            $status = "Closed";

            $stmt3->bindParam(':post_id', $bid->post_id);
            $stmt3->bindParam(':status', $status);

            $stmt3->execute();

            $response = [
                "status" => "success",
                "message" => "deal successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "deal failed"
            ];
        }

        echo json_encode($response);
        break;
}
