CREATE TABLE cats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

INSERT INTO cats (name) VALUES ('Marmite'), ('Tango');

CREATE TABLE feed_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    human_name VARCHAR(50) NOT NULL,  -- the human
    note TEXT,
    fed_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE feed_log_cats (
    feed_log_id INT,
    cat_id INT,
    PRIMARY KEY (feed_log_id, cat_id),
    FOREIGN KEY (feed_log_id) REFERENCES feed_log(id),
    FOREIGN KEY (cat_id) REFERENCES cats(id)
);