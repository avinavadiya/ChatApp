<?php
$name1 = $_GET['user1'];
$name2 = $_GET['user2'];

$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "chatapp";

$conn = mysqli_connect($hostname, $username, $password, $dbname);
if (!$conn) {
    echo "Database connection error" . mysqli_connect_error();
}

$sql2 = "SELECT unique_id FROM users WHERE fname = '$name1'";
$result2 = mysqli_query($conn, $sql2);
while ($row2 = mysqli_fetch_assoc($result2)) {
    $incoming_id = $row2['unique_id'];
}


$sql3 = "SELECT unique_id FROM users WHERE fname = '$name2'";
$result3 = mysqli_query($conn, $sql3);
while ($row3 = mysqli_fetch_assoc($result3)) {
    $outgoing_id = $row3['unique_id'];
}

$sql = "SELECT * FROM messages LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id})
OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id}) ORDER BY msg_id";
$result = mysqli_query($conn, $sql);

$filename = "chat_history($name1 & $name2).txt";
$file = fopen($filename, "w");

while ($row = mysqli_fetch_assoc($result)) {
    $message = $row['msg'];
    $sender = $row['incoming_msg_id'];
    $timestamp = $row['time'];
    // $text = "$sender: $message          [$timestamp]\n";
    $text = sprintf("%-10s %-40s [%s]\n", $sender . ":", $message, $timestamp);
    fwrite($file, $text);
}


$file_contents = file_get_contents($filename);
$search_text = $incoming_id;
$replace_text = $name1;
$modified_contents = str_replace($search_text, $replace_text, $file_contents);
file_put_contents($filename, $modified_contents);


$file_contents = file_get_contents($filename);
$search_text = $outgoing_id;
$replace_text = $name2;
$modified_contents = str_replace($search_text, $replace_text, $file_contents);
file_put_contents($filename, $modified_contents);

fclose($file);

header("Content-disposition: attachment; filename=$filename");
header("Content-type: text/plain");
readfile($filename);

mysqli_close($conn);

?>