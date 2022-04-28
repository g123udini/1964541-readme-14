<?php
require_once 'helpers.php';
require_once 'functions.php';
require_once 'data.php';
require_once 'session.php';




$tab  = filter_input(INPUT_GET, 'tab');
$params = filter_input(INPUT_GET, 'tab');
$is_type = [];

if ($tab) {
    $sql = 'SELECT posts.id, title, text, quote_auth, img, video, link, views, posts.dt_add, login, avatar, type FROM posts' .
        ' JOIN users u ON posts.user_id = u.id' .
        ' JOIN content_type ct' .
        ' ON posts.content_type_id = ct.id' .
        ' WHERE ct.type = ?' .
        ' ORDER BY views DESC;';
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, 's', $tab);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $sql = 'SELECT posts.id, title, text, quote_auth, img, video, link, views, posts.dt_add, login, avatar, type FROM posts' .
        ' JOIN users u ON posts.user_id = u.id' .
        ' JOIN content_type ct' .
        ' ON posts.content_type_id = ct.id' .
        ' ORDER BY views DESC;';
    $result = mysqli_query($connection, $sql);
}

if ($result) {
    $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $error = mysqli_error($connection);
    $page_content = include_template('error.php', ['error' => $error]);
}


$sql = 'SELECT id, name, type FROM content_type;';
$result = mysqli_query($connection, $sql);


if ($result) {
    $content_types = mysqli_fetch_all($result, MYSQLI_ASSOC);
    foreach ($content_types as $content_type) { //проверка на тип контента
        if ($content_type['type'] === $tab) {
            $is_type = 'Есть тип';
        }
    }
    if (!$is_type && !empty($tab)) {
        header('Location: error.php?code=404');
        exit;
    }
    $page_content = include_template('popular_templates/main.php', [
        'posts' => $posts,
        'content_types' => $content_types,
        'tab' => $tab]);
} else {
    $error = mysqli_error($connection);
    $page_content = include_template('error.php', ['error' => $error]);
}



$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: блог, каким он должен быть',
    'user_name' => $user_name]);
print($layout_content);


?>

