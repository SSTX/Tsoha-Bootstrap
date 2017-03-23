-- Lisää CREATE TABLE lauseet tähän tiedostoon
CREATE TABLE registered_user 
(
    user_id SERIAL PRIMARY KEY,
    user_name varchar(50), 
    user_pw_hash text
);


CREATE TABLE file_metadata 
(
    file_id SERIAL PRIMARY KEY,
    file_author INTEGER REFERENCES registered_user(user_id),
    file_name text,
    file_description text,
    file_submit_time timestamp with time zone,
    file_path text,
    file_size bigint,
    file_type text
);

CREATE TABLE message 
(
    message_id SERIAL PRIMARY KEY, 
    message_author INTEGER REFERENCES registered_user(user_id), 
    message_related_file INTEGER REFERENCES file_metadata(file_id), 
    message_subject text, 
    message_body text, 
    message_submit_time timestamp with time zone
);

CREATE TABLE tag 
(
    tag_id SERIAL PRIMARY KEY,
    tag_name text,
    tag_description text
);

CREATE TABLE tagged_file 
(
    tagged_file INTEGER REFERENCES file_metadata(file_id),
    tag INTEGER REFERENCES tag(tag_id)
);
