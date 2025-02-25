<?php

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL & E_NOTICE & E_WARNING);
require_once 'helpers.php';
require_once 'functions.php';
require_once 'data.php';
require_once 'session.php';

$post_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$validation_errors = [];
$tags = [];
$navigation_link = 'post.php';
$comments_offset = ' LIMIT 3;';

$tab = filter_input(INPUT_GET, 'tab');

if ($tab === 'comments_all') {
    $comments_offset = ';';
}

$sql = 'SELECT p.id, title, text, quote_auth, img, video, link, views, p.dt_add, user_id, type,' .
    ' (SELECT COUNT(post_id)' .
    ' FROM likes' .
    ' WHERE likes.post_id = p.id)' .
    ' AS likes,' .
    ' (SELECT COUNT(content) FROM comments' .
    ' WHERE post_id = p.id)' .
    ' AS comment_sum,' .
    ' (SELECT COUNT(original_id) FROM posts' .
    ' WHERE original_id = p.id)' .
    ' AS reposts_sum' .
    ' FROM posts p' .
    ' JOIN users u ON p.user_id = u.id' .
    ' JOIN content_type ct' .
    ' ON p.content_type_id = ct.id' .
    ' WHERE p.id = ?';

$stmt = mysqli_prepare($connection, $sql);
mysqli_stmt_bind_param($stmt, 'i', $post_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);


if ($result) {
    $post = mysqli_fetch_assoc($result);
    $user_info = get_user_info($connection, $post['user_id']);
    $this_user = get_user($connection, $post['user_id']);
    $is_subscribe = check_subscription($connection, $this_user['id'], $user['user_id']);

    $sql = 'UPDATE posts SET views = views + 1 WHERE id = ?;';
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $post_id);
    mysqli_stmt_execute($stmt);

    $tags = get_tags($connection, $post_id);

    $sql = 'SELECT content, user_id, c.dt_add, login, avatar' .
        ' FROM comments c' .
        ' JOIN users u ON c.user_id = u.id' .
        ' WHERE post_id = ?' . $comments_offset;
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $post_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $comments = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $required = ['comment', 'post_id'];
        $rules = [
            'comment' => function ($value) {
                return validate_comment($value, COMMENT_MIN_LENGTH);
            },
            'post_id' => function ($value) use ($connection) {
                return validate_post_id($connection, $value);
            }
        ];

        $comment = filter_input_array(INPUT_POST, [
            'comment' => FILTER_DEFAULT,
            'post_id' => FILTER_VALIDATE_INT
        ], true);

        $validation_errors = full_form_validation($comment, $rules, $required);

        if (!$validation_errors) {
            $sql = 'INSERT INTO comments (content, user_id, post_id)' .
                ' VALUES (?, ?, ?)';
            $stmt = mysqli_prepare($connection, $sql);
            mysqli_stmt_bind_param($stmt, 'sii', $comment['comment'], $user['user_id'], $comment['post_id']);
            mysqli_stmt_execute($stmt);
            header('Location: users_profile.php?id=' . $this_user['id']);
            exit;
        }
    }

    $page_content = include_template('post_templates/post-window.php', [
        'post' => $post,
        'tags' => $tags,
        'tab' => $tab,
        'comments' => $comments,
        'user_info' => $user_info,
        'this_user' => $this_user,
        'is_subscribe' => $is_subscribe,
        'user' => $user,
        'validation_errors' => $validation_errors
    ]);
} else {
    $error = mysqli_error($connection);
    $page_content = include_template('error.php', ['error' => $error]);
}


$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: блог, каким он должен быть',
    'user' => $user,
    'navigation_link' => $navigation_link,
    'message_notification' => $message_notification
]);
print($layout_content);
