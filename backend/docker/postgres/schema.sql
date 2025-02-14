-- Create the database schema for ReserveIt

-- Create rooms table
CREATE TABLE room (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

-- Create reservations table
CREATE TABLE reservation (
    id SERIAL PRIMARY KEY,
    room_id INT NOT NULL,
    reserved_by VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    CONSTRAINT fk_room
        FOREIGN KEY(room_id) 
        REFERENCES room(id)
        ON DELETE CASCADE,
    CONSTRAINT check_end_time_after_start_time
        CHECK (end_time > start_time)
);

-- Create index for faster reservation lookups
CREATE INDEX idx_reservation_room_date ON reservation(room_id, date);

-- Create index for checking overlapping reservations
CREATE INDEX idx_reservation_time ON reservation(room_id, date, start_time, end_time);
