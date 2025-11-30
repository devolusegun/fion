<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require "db.php";
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $category = $_POST["category"];
    $title = $_POST["title"];
    $summary = $_POST["summary"];
    $justification = $_POST["justification"];
    $budget = $_POST["estimated_budget"];
    $location = $_POST["location"];

    // Create uploads folder if missing
    if (!is_dir("uploads")) {
        mkdir("uploads", 0777, true);
    }

    // File uploads
    $qual = "uploads/" . time() . "_qual_" . basename($_FILES["qualification"]["name"]);
    $prop = "uploads/" . time() . "_proposal_" . basename($_FILES["proposal"]["name"]);

    move_uploaded_file($_FILES["qualification"]["tmp_name"], $qual);
    move_uploaded_file($_FILES["proposal"]["tmp_name"], $prop);

    // Insert into DB
    $stmt = $conn->prepare("
        INSERT INTO project_ideas 
        (user_id, category, title, summary, justification, estimated_budget, location, qualification_doc, proposal_doc) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("issssssss", 
        $user_id, 
        $category, 
        $title, 
        $summary, 
        $justification, 
        $budget, 
        $location, 
        $qual, 
        $prop
    );

    $stmt->execute();

    // Create admin notification
    $msg = "New project idea submitted in category: $category";
    $notify = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $notify->bind_param("is", $user_id, $msg);
    $notify->execute();

    echo "<p>Project idea submitted successfully.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Project Idea</title>
</head>
<body>

<h2>Submit Project Idea</h2>

<form method="POST" enctype="multipart/form-data">

    <label>Category</label><br>
    <select name="category" required>
        <option value="">-- Select Category --</option>
        <option value="dredging_river_niger">Dredging of River Niger & Upper Benue (Inland Ports)</option>
        <option value="steel_industry">Steel Industry & Heavy-Duty Equipment Factories</option>
        <option value="retail_system_reform">Retail System Reform & National Co-operative Capital Base</option>
        <option value="land_geopin_mapping">Land Mapping via Geopin & Ownership System</option>
        <option value="water_dam_city_planning">Water Dam, Pipeborne Water & City/Town Planning</option>
    </select>
    <br><br>

    <label>Project Title</label><br>
    <input type="text" name="title" required><br><br>

    <label>Summary (Short Description)</label><br>
    <textarea name="summary" rows="3" required></textarea><br><br>

    <label>Justification (Why is this important?)</label><br>
    <textarea name="justification" rows="5" required></textarea><br><br>

    <label>Estimated Budget</label><br>
    <input type="text" name="estimated_budget" placeholder="e.g., ₦500 million or $2.3M"><br><br>

    <label>Project Location</label><br>
    <input type="text" name="location" placeholder="City, Region, State"><br><br>

    <label>Qualification Document</label><br>
    <input type="file" name="qualification" required><br><br>

    <label>Idea Proposal Document</label><br>
    <input type="file" name="proposal" required><br><br>

    <button type="submit">Submit Idea</button>

</form>

</body>
</html>
