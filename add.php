<?php
require_once 'helpers.php';
require_once 'functions.php';
require_once 'data.php';

$sql = 'SELECT id, name, type FROM content_type;';
$result = mysqli_query($connection, $sql);
$content_types = mysqli_fetch_all($result, MYSQLI_ASSOC);


$page_content = include_template('add_templates/adding-post.php', ['content_types' => $content_types]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rules = [
        'title' => function ($value) {
            return validate_filled($value);
        },
        'tags' => function ($value) {
            return validate_tag($value);
        },
        'photo-link' => function ($value) {
            return validate_photo_link($value);
        },
        'video' => function($value) {
            return validate_video($value);
        }
    ];
    $required = ['title', 'tags'];
    $validation_errors = [];

    $post = filter_input_array(INPUT_POST, [
        'title' => FILTER_DEFAULT,
        'text' => FILTER_DEFAULT,
        'quote_auth' => FILTER_DEFAULT,
        'photo-link' => FILTER_VALIDATE_URL,
        'video' => FILTER_VALIDATE_URL,
        'link' => FILTER_VALIDATE_URL,
        'tags' => FILTER_DEFAULT,], true);

    // Валидация файла
    var_dump($_FILES);
    if (!empty($_FILES['userpic-file-photo']['name'])) {
        $tmp_name = $_FILES['userpic-file-photo']['tmp-name'];
        $path = $_FILES['userpic-file-photo']['name'];
        $filename = uniqid() . '.jpg';

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $tmp_name);
        if ($file_type !== 'image/gif' || $file_type !== 'image/jpeg' || $file_type !== 'image/png') {
            $validation_errors['file'] = 'Загрузите файл формата gif, jpeg или png';
        } else {
            move_uploaded_file($tmp_name, '/uploads' . $filename);
            $post['photo-link'] = $filename;
        }
    } else if (empty($post['photo-link'])) {
        $validation_errors['file'] = 'Вы не загрузили файл';
    }

    if (!isset($_FILES['userpic-file-photo']['name'])) { //если файл с фото не добавлен, то проверяем ссылку.
        $required[] = 'photo-link';                      //но надо добавить проверку вида контента
    }


    foreach ($post as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $validation_errors[$key] = $rule($value);
        }
        if (in_array($key, $required) && empty($value)) {
            $validation_errors[$key] = 'Поле ' . $key . ' надо заполнить';
        }
    }
    $validation_errors = array_diff($validation_errors, array(''));

    if ($validation_errors) {
        $page_content = include_template('add_templates/adding-post.php', ['content_types' => $content_types, 'validation_errors' => $validation_errors]);
    } else {
        $sql = 'INSERT INTO tags (name) VALUE (?)';
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, 's', $tag);
        if (stristr($post['tags'], ' ')) {
            $tags = explode(' ', $post['tags']);
            foreach ($tags as $tag) {
                mysqli_stmt_execute($stmt);
                $tags_id = [];
                $tags_id = mysqli_insert_id($connection);
            }
        } else {
            $tag = $post['tags'];
            mysqli_stmt_execute($stmt);
            $tag_id = mysqli_insert_id($connection);
        }
        unset($post['tags']);


        $sql = 'INSERT INTO posts (title, text, quote_auth, img, video, link, user_id, content_type_id) VALUES (?, ?, ?, ?, ?, ?, 1, $content_type_id)';
        $stmt = db_get_prepare_stmt($connection, $sql, $post);
        $result = mysqli_stmt_execute($stmt);
        if ($result) {
            $post_id = mysqli_insert_id($connection);
            header('Location: post.php?id=' . $post_id);
        } else {
            $page_content = include_template('error.php', ['error' => $error]);
        }
    }
}


$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: блог, каким он должен быть',
    'is_auth' => $is_auth,
    'user_name' => $user_name]);
print($layout_content);

