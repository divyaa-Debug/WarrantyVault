<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include "config/db.php";

$user_id = $_SESSION["user_id"];

if (!isset($_GET["id"])) {
    header("Location: products.php");
    exit();
}

$product_id = $_GET["id"];

// Delete only the product belonging to the logged-in user
$stmt = $conn->prepare(
    "DELETE FROM products WHERE id = ? AND user_id = ?"
);

$stmt->bind_param("ii", $product_id, $user_id);

$stmt->execute();

header("Location: products.php");
exit();

?>