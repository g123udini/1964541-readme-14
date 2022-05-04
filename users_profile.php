<?php
require_once 'functions.php';
require_once 'helpers.php';
require_once 'data.php';
require_once 'session.php';

$user_id = filter_input(INPUT_GET, 'id');
$sql = 'SELECT id, login, avatar, dt_add' .
    ' FROM users u' .
    ' WHERE id = ?;';
$stmt = mysqli_prepare($connection, $sql);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$this_user = mysqli_fetch_assoc($result);

if ($this_user) {
    $sql = 'SELECT DISTINCT p.id, title, text, quote_auth, img, video, title, text, quote_auth, img, video, link, views, user_id, p.dt_add, login, type, avatar' .
        ' FROM posts p' .
        ' JOIN users u ON u.id = p.user_id' .
        ' JOIN content_type ct ON p.content_type_id = ct.id' .
        ' WHERE u.id = ?;';
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $follow_id = $this_user['id'];
    $follower_id = $user['user_id'];
    $sql = 'SELECT * FROM subscribes' .
        ' WHERE follow_id = ? AND follower_id = ?;';
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt,'ii', $follow_id, $follower_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $result = mysqli_fetch_assoc($result);
    $is_subscribe = true;
    if ($result) {
        $is_subscribe = false;
    }

    $sql = 'SELECT (SELECT COUNT(p.id)' .
        ' FROM posts p' .
        ' WHERE p.user_id = u.id)' .
        ' AS posts_count,' .
        '(SELECT COUNT(follower_id)' .
        ' FROM subscribes s' .
        ' WHERE s.follow_id = u.id)' .
        ' AS subscribers_count' .
        ' FROM posts p' .
        ' JOIN users u ON p.user_id = u.id' .
        ' WHERE u.id = ?' .
        ' GROUP BY posts_count, subscribers_count;';
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user_info = mysqli_fetch_assoc($result);

} else {
    header('Location: error.php?code=404');
    exit;
}


$page_content = include_template('profile_templates/users-profile-window.php', ['this_user' => $this_user, 'posts' => $posts, 'user_info' => $user_info, 'is_subscribe' => $is_subscribe]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: блог, каким он должен быть',
    'this_user' => $this_user,
    'user' => $user]);
print($layout_content);
