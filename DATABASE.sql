-- Drop if exists to avoid conflicts
DROP TABLE IF EXISTS Reviews, Inquiry, House, Student, Caretaker, Admin;

-- Student Table
CREATE TABLE Student (
    studentId INT PRIMARY KEY AUTO_INCREMENT,
    studentName VARCHAR(100),
    studentEmail VARCHAR(100) UNIQUE,
    studentPhoneNumber VARCHAR(20),
    studentPassword VARCHAR(255)
);

-- Caretaker Table
CREATE TABLE Caretaker (
    caretakerId INT PRIMARY KEY AUTO_INCREMENT,
    caretakerName VARCHAR(100),
    caretakerEmail VARCHAR(100) UNIQUE,
    caretakerPhoneNumber VARCHAR(20),
    caretakerPassword VARCHAR(255),
    isVerified BOOLEAN DEFAULT FALSE, /*I'm gonna use this instead of creating a whole other table for pending requests to avoid duplication and then I'm gonna use WHERE isVerified = FALSE in the admin page to get the pending requests*/
    verificationDate DATE
);

-- Admin Table
CREATE TABLE Admin (
    adminId INT PRIMARY KEY AUTO_INCREMENT,
    adminName VARCHAR(100),
    adminEmail VARCHAR(100) UNIQUE,
    adminPhoneNumber VARCHAR(20),
    adminPassword VARCHAR(255)
);

-- House Table
CREATE TABLE House (
    houseId INT PRIMARY KEY AUTO_INCREMENT,
    houseTitle VARCHAR(100),
    houseLocation VARCHAR(100),
    housePrice FLOAT,
    houseDescription TEXT,
    imageUrl VARCHAR(255),
    isApproved BOOLEAN DEFAULT FALSE,
    approvalDate DATE,
    caretakerId INT,
    FOREIGN KEY (caretakerId) REFERENCES Caretaker(caretakerId)
        ON DELETE SET NULL
);

-- Inquiry Table
CREATE TABLE Inquiry (
    inquiryId INT PRIMARY KEY AUTO_INCREMENT,
    studentId INT,
    houseId INT,
    inquiryMessage TEXT,
    inquiryStatus VARCHAR(50),
    inquiryDate DATE,
    FOREIGN KEY (studentId) REFERENCES Student(studentId)
        ON DELETE CASCADE,
    FOREIGN KEY (houseId) REFERENCES House(houseId)
        ON DELETE CASCADE
);

-- Reviews Table
CREATE TABLE Reviews (
    reviewId INT PRIMARY KEY AUTO_INCREMENT,
    reviewerId INT,     -- Student writing the review
    reviewedId INT,     -- Caretaker being reviewed
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    FOREIGN KEY (reviewerId) REFERENCES Student(studentId)
        ON DELETE CASCADE,
    FOREIGN KEY (reviewedId) REFERENCES Caretaker(caretakerId)
        ON DELETE CASCADE
);

-- Table for Pending House Listings
CREATE TABLE PendingHouse (
    pendingHouseId INT PRIMARY KEY AUTO_INCREMENT,
    houseTitle VARCHAR(100),
    houseLocation VARCHAR(100),
    housePrice FLOAT,
    houseDescription TEXT,
    imageUrl VARCHAR(255),
    caretakerId INT,
    submissionDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (caretakerId) REFERENCES Caretaker(caretakerId)
        ON DELETE SET NULL
);

CREATE TABLE RejectedCaretaker (
    rejectionId INT AUTO_INCREMENT PRIMARY KEY,
    caretakerId INT,
    caretakerName VARCHAR(100),
    caretakerEmail VARCHAR(100),
    caretakerPhoneNumber VARCHAR(15),
    rejectionReason TEXT,
    rejectionDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE RejectedHouse (
    rejectionId INT AUTO_INCREMENT PRIMARY KEY,
    pendingHouseId INT,
    houseTitle VARCHAR(100),
    houseLocation VARCHAR(100),
    housePrice FLOAT,
    houseDescription TEXT,
    imageUrl VARCHAR(255),
    caretakerId INT,
    rejectionReason TEXT,
    rejectionDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE HouseImages (
    imageId INT AUTO_INCREMENT PRIMARY KEY,
    houseId INT,
    imageUrl VARCHAR(255),
    FOREIGN KEY (houseId) REFERENCES House(houseId) ON DELETE CASCADE
);

-- Adding field for inquiry response
ALTER TABLE inquiry ADD inquiryResponse TEXT;

CREATE TABLE PendingHouseImages (
    imageId INT AUTO_INCREMENT PRIMARY KEY,
    houseId INT NOT NULL,
    imageUrl VARCHAR(255) NOT NULL,
    FOREIGN KEY (houseId) REFERENCES PendingHouse(pendingHouseId) ON DELETE CASCADE
);

ALTER TABLE House DROP COLUMN imageUrl;

