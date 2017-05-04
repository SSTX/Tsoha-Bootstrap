-- Lisää CREATE TABLE lauseet tähän tiedostoon
CREATE TABLE registered_user 
(
    user_id SERIAL PRIMARY KEY,
    user_name text, 
    user_pw_hash text, 
    user_pw_salt text,
    user_register_time timestamp with time zone DEFAULT current_timestamp,
    user_is_admin boolean DEFAULT FALSE
);


CREATE TABLE file_metadata 
(
    file_id SERIAL PRIMARY KEY,
    file_author INTEGER REFERENCES registered_user(user_id) ON DELETE SET NULL,
    file_name text,
    file_description text,
    file_submit_time timestamp with time zone DEFAULT current_timestamp,
    file_path text,
    file_size bigint,
    file_type text
);

CREATE TABLE message 
(
    message_id SERIAL PRIMARY KEY, 
    message_author INTEGER REFERENCES registered_user(user_id) ON DELETE CASCADE, 
    message_related_file INTEGER REFERENCES file_metadata(file_id) ON DELETE CASCADE, 
    message_subject text, 
    message_body text, 
    message_submit_time timestamp with time zone DEFAULT current_timestamp
);

CREATE TABLE tag 
(
    tag_id SERIAL PRIMARY KEY,
    tag_name text
);

CREATE TABLE tagged_file 
(
    tagged_file INTEGER REFERENCES file_metadata(file_id) ON DELETE CASCADE,
    tag INTEGER REFERENCES tag(tag_id) ON DELETE CASCADE
);
