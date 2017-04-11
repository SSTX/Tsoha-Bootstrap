-- Lisää INSERT INTO lauseet tähän tiedostoon
INSERT INTO registered_user (user_name, user_pw_hash) 
VALUES ('Jalmari', '$2y$10$aYKL9iMH/39neNjVi1eZ2eSCLZ0C3NqX8A9glAmiTd4dQ36e5YRAG');

INSERT INTO tag (tag_name, tag_description) 
VALUES ('test tag', 'this is a tag for testing');


INSERT INTO file_metadata (file_author, file_name, file_description, file_submit_time, file_path, file_size, file_type)
VALUES (1, 'test file', 'testing', current_timestamp, 'files/testfile.txt', 3000, 'text/plain');

INSERT INTO message (message_author, message_related_file, message_subject, message_body, message_submit_time)
VALUES (1, 1, 'test message', 'testing', current_timestamp);

INSERT INTO tagged_file (tagged_file, tag)
VALUES (1, 1);
