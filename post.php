<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":

        if (isset($_GET['user_id'])) {
            $user_id_specific_user = $_GET['user_id'];
            $sql = "SELECT * FROM post WHERE user_id = :user_id";
        }

        if (isset($_GET['post_id'])) {
            $post_id = $_GET['post_id'];

            $sql = "SELECT * FROM post 
            INNER JOIN users ON post.user_id = users.user_id 
            WHERE post.post_id = :post_id
            ORDER BY post.post_id DESC";
        }


        if (!isset($_GET['user_id']) && !isset($_GET['post_id'])) {
            $sql = "SELECT * FROM post INNER JOIN users ON post.user_id = users.user_id ORDER BY post_id DESC";
        }


        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($user_id_specific_user)) {
                $stmt->bindParam(':user_id', $user_id_specific_user);
            }

            if (isset($post_id)) {
                $stmt->bindParam(':post_id', $post_id);
            }

            $stmt->execute();
            $user_post = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($user_post);
        }


        break;

    case "POST":
        $user_post = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO post (user_id, post_context, post_image, project_location, project_name, email_phone, starting_price, created_at, close_until) VALUES (:user_id, :post_context, :post_image, :project_location, :project_name, :email_phone, :starting_price, :created_at, :close_until)";
        $stmt = $conn->prepare($sql);

        $created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(':user_id', $user_post->user_id);
        $stmt->bindParam(':post_context', $user_post->post_context);
        $stmt->bindParam(':post_image', $user_post->post_image);
        $stmt->bindParam(':project_location', $user_post->project_location);
        $stmt->bindParam(':project_name',  $user_post->project_name);
        $stmt->bindParam(':email_phone',  $user_post->email_phone);
        $stmt->bindParam(':starting_price',  $user_post->starting_price);
        $stmt->bindParam(':close_until',  $user_post->close_until);

        $stmt->bindParam(':created_at',  $created_at);


        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "post successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "post failed"
            ];
        }




        echo json_encode($response);
        break;

    case "PUT":
        $user_post = json_decode(file_get_contents('php://input'));
        $sql = "UPDATE post 
        SET post_context = :post_context,
            post_image = :post_image,
            project_location = :project_location,
            project_name = :project_name,
            email_phone = :email_phone,
            starting_price = :starting_price,
            close_until = :close_until
        WHERE post_id = :post_id";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':post_id', $user_post->post_id);
        $stmt->bindParam(':post_context', $user_post->post_context);
        $stmt->bindParam(':post_image', $user_post->post_image);
        $stmt->bindParam(':project_location', $user_post->project_location);
        $stmt->bindParam(':project_name', $user_post->project_name);
        $stmt->bindParam(':email_phone', $user_post->email_phone);
        $stmt->bindParam(':starting_price', $user_post->starting_price);
        $stmt->bindParam(':close_until', $user_post->close_until);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "post updated successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "post update failed"
            ];
        }

        break;
    case "DELETE":
        $user_post = json_decode(file_get_contents('php://input'));
        $sql = "DELETE FROM post WHERE post_id = :post_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':post_id', $user_post->post_id);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "user_post deleted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "user_post delete failed"
            ];
        }

        echo json_encode($response);
        break;
}
