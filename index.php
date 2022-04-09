<?php
require_once 'helpers.php';
require_once 'functions.php';
require_once 'data.php';

$is_auth = rand(0, 1);

$user_name = 'Кирилл';

if (!$con) {
    print ('Error: ' . mysqli_connect_error($con));
} else {
    $sql = 'SELECT * FROM posts' .
        ' JOIN users u ON posts.user_id = u.id ORDER BY views DESC;';
    if ($result = mysqli_query($con, $sql)) {
        $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        print ('Error: ' . mysqli_error($con));
    }
}

$page_content = include_template('main.php', ['posts' => $posts]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: блог, каким он должен быть',
    'is_auth' => $is_auth,
    'user_name' => $user_name]);
print($layout_content);

?>

