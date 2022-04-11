USE readme;

#заполняем юзеров
INSERT INTO users (email, password, login, avatar)
VALUES ('4204884@gmail.com', 'WASH32rh' , 'gervant of irvia', '/img/cat.jpg'),
       ('larisa@gmail.com', 'QWERTY1234', 'Лариса', 'userpic-larisa-small.jpg'),
       ('volandeslav@gmail.com', 'BUric1hK', 'Владик', 'userpic.jpg'),
       ('dargin@mail.ru', 'SWAGmad', 'Виктор', 'userpic-mark.jpg');

#заполняем посты
INSERT INTO posts (title, text, quote_auth, img, video, link, views, content_type, user_id)
VALUES ('Цитата', 'Мы в жизни любим только раз, а после ищем лишь похожих', 'Неизвестный автор', NULL, NULL, NULL, 7, 'post-quote', '2'),
       ('Игра престолов', 'Власть пребывает там, куда помещает её всеобщая вера. Это уловка, тень на стене.', NULL, NULL, NULL, NULL, 5, 'post-text', '3'),
       ('Наконец обработал фотки!', NULL, NULL, 'rock-medium.jpg', NULL, NULL, 2, 'post-photo', '4'),
       ('Моя мечта', NULL, NULL, 'coast-medium.jpg', NULL, NULL, 10, 'post-photo', '2'),
       ('Лучшие курсы', NULL, NULL, NULL, NULL, 'www.htmlacademy.ru/', 20, 'post-link', '3');

#заполняем комменты
INSERT INTO comments (content, post_id, user_id)
VALUES ('Безумно можно быть первым!', '1', '1'),
       ('Последний сезон слил весь сериал...', '2', '3'),
       ('Согласена, я в полном восторге!', '5', '2');

#заполняем типы контента
INSERT INTO content_type (name, type)
VALUE ('Текст', 'post-text'),
      ('Цитата', 'post-quote'),
      ('Картинка', 'post-photo'),
      ('Ссылка', 'post-link'),
      ('Видео', 'post-video');

#список постов с сортировкой по популярности вместе с именами авторов и типом контента
SELECT title, login, name
FROM posts
JOIN users u
ON posts.user_id = u.id
JOIN content_type ct ON posts.content_type_id = ct.id
ORDER BY views DESC;

#получить список постов для конкретного пользователя
SELECT login, title, text, quote_auth, img, video, link, views
FROM posts
JOIN users u
ON posts.user_id = u.id
WHERE u.login = 'Владик';

#получить список комментариев для одного поста, в комментариях должен быть логин пользователя
SELECT login, content
FROM comments
JOIN users u
ON comments.user_id = u.id
WHERE post_id = 2;

#добавить лайк к посту
INSERT INTO likes (user_id, post_id)
VALUE (2, 1);

#Лариса подписалась на Владика
INSERT INTO subscribes (follower_id, follow_id)
VALUE (2, 3);

ALTER TABLE posts DROP content_type_id;
ALTER TABLE posts ADD content_type_id INT, ADD
FOREIGN KEY (content_type_id) REFERENCES content_type(id);
ALTER TABLE content_type ADD UNIQUE INDEX UI_type (type);

UPDATE posts SET posts.content_type_id = 2 WHERE id = 1;
UPDATE posts SET posts.content_type_id = 1 WHERE id = 2;
UPDATE posts SET posts.content_type_id = 3 WHERE id = 3;
UPDATE posts SET posts.content_type_id = 3 WHERE id = 4;
UPDATE posts SET posts.content_type_id = 4 WHERE id = 5;

