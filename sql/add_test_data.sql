-- Lisää INSERT INTO lauseet tähän tiedostoon
INSERT INTO registered_user (user_name, user_pw_hash, user_pw_salt, user_register_time) 
VALUES ('4423', '$2a$10$13340b662369ee3121811usNixBJY9erHXiaNx4jtgT87ctYqDuZ.', '$2a$10$13340b662369ee312181142dbfd9f144', current_timestamp);

INSERT INTO tag (tag_name, tag_description) 
VALUES ('test tag', 'this is a tag for testing');


INSERT INTO file_metadata (file_author, file_name, file_description, file_submit_time, file_path, file_size, file_type)
VALUES (1, 'test file', 'testing', current_timestamp, 'files/testfile.txt', 3000, 'text/plain');

INSERT INTO message (message_author, message_related_file, message_subject, message_body, message_submit_time)
VALUES (1, 1, 'test message', 'testing', current_timestamp);

INSERT INTO tagged_file (tagged_file, tag)
VALUES (1, 1);
