<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":

        if (isset($_GET['post_id'])) {
            $post_id = $_GET['post_id'];
            $sql = "SELECT bidding.created_at, bidding.bidding_comment, bidding.bidding_price FROM bidding WHERE post_id = :post_id ORDER BY bidding_id DESC";
        }




        if (!isset($_GET['post_id']) && !isset($_GET['post_id'])) {
            $sql = "SELECT * FROM post INNER JOIN users ON post.user_id = users.user_id ORDER BY post_id DESC";
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
        $sql = "INSERT INTO bidding (bidding_price, bidding_current_price, bidder_id, post_id, bidding_comment, created_at) VALUES (:bidding_price, :bidding_current_price, :bidder_id, :post_id, :bidding_comment, :created_at)";
        $stmt = $conn->prepare($sql);

        $created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(':bidder_id', $bid->bidder_id);
        $stmt->bindParam(':bidding_price', $bid->bidding_price);
        $stmt->bindParam(':post_id', $bid->post_id);
        $stmt->bindParam(':bidding_comment',  $bid->bidding_comment);
        $stmt->bindParam(':bidding_current_price',  $bid->bidding_current_price);
        $stmt->bindParam(':created_at',  $created_at);


        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "bidding successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "bidding failed"
            ];
        }




        echo json_encode($response);
        break;
}
