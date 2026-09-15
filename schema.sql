-- Create Database
CREATE DATABASE IF NOT EXISTS `advanced_school_db`;
USE `advanced_school_db`;

-- Disable foreign key checks during creation sequence
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Table Name: departments
-- Purpose: Stores independent institutional department data.
DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
  `dept_id` INT AUTO_INCREMENT,
  `dept_name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`dept_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Table Name: instructors
-- Purpose: Tracks faculty instructors. Links 1:M with departments.
DROP TABLE IF EXISTS `instructors`;
CREATE TABLE `instructors` (
  `instructor_id` INT AUTO_INCREMENT,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `dept_id` INT NULL,
  PRIMARY KEY (`instructor_id`),
  CONSTRAINT `fk_instructors_departments` 
    FOREIGN KEY (`dept_id`) REFERENCES `departments` (`dept_id`) 
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Table Name: office_details
-- Purpose: Records unique physical office allocations. Links 1:1 with instructors.
DROP TABLE IF EXISTS `office_details`;
CREATE TABLE `office_details` (
  `office_id` INT AUTO_INCREMENT,
  `instructor_id` INT NOT NULL UNIQUE,
  `building_name` VARCHAR(50) NOT NULL,
  `room_number` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`office_id`),
  CONSTRAINT `fk_office_details_instructors` 
    FOREIGN KEY (`instructor_id`) REFERENCES `instructors` (`instructor_id`) 
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Table Name: instructor_specializations
-- Purpose: Maps multi-valued domain skill areas for instructors. Links 1:M with instructors.
DROP TABLE IF EXISTS `instructor_specializations`;
CREATE TABLE `instructor_specializations` (
  `spec_id` INT AUTO_INCREMENT,
  `instructor_id` INT NOT NULL,
  `skill_area` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`spec_id`),
  CONSTRAINT `fk_specializations_instructors` 
    FOREIGN KEY (`instructor_id`) REFERENCES `instructors` (`instructor_id`) 
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Table Name: courses
-- Purpose: Houses the master catalog course curriculum blueprints. Links 1:M with departments.
DROP TABLE IF EXISTS `courses`;
CREATE TABLE `courses` (
  `course_id` INT AUTO_INCREMENT,
  `course_code` VARCHAR(10) NOT NULL UNIQUE,
  `course_title` VARCHAR(150) NOT NULL,
  `credits` INT NOT NULL DEFAULT 3,
  `dept_id` INT NULL,
  PRIMARY KEY (`course_id`),
  CONSTRAINT `fk_courses_departments` 
    FOREIGN KEY (`dept_id`) REFERENCES `departments` (`dept_id`) 
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Table Name: classrooms
-- Purpose: Logistical room facilities database matrix.
DROP TABLE IF EXISTS `classrooms`;
CREATE TABLE `classrooms` (
  `room_id` INT AUTO_INCREMENT,
  `room_name` VARCHAR(50) NOT NULL,
  `capacity` INT NOT NULL,
  PRIMARY KEY (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Table Name: classes
-- Purpose: Active scheduled course instances. Serves as a composite bridge linking courses, instructors, and rooms.
DROP TABLE IF EXISTS `classes`;
CREATE TABLE `classes` (
  `class_id` INT AUTO_INCREMENT,
  `course_id` INT NOT NULL,
  `instructor_id` INT NOT NULL,
  `room_id` INT NOT NULL,
  `semester` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`class_id`),
  CONSTRAINT `fk_classes_courses` 
    FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) 
    ON DELETE CASCADE,
  CONSTRAINT `fk_classes_instructors` 
    FOREIGN KEY (`instructor_id`) REFERENCES `instructors` (`instructor_id`) 
    ON DELETE CASCADE,
  CONSTRAINT `fk_classes_classrooms` 
    FOREIGN KEY (`room_id`) REFERENCES `classrooms` (`room_id`) 
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Table Name: students
-- Purpose: Tracks core student identities and administrative baseline metrics.
DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
  `student_id` INT AUTO_INCREMENT,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `enrollment_date` DATE NOT NULL,
  PRIMARY KEY (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Table Name: enrollments
-- Purpose: Core M:M Junction Table intersecting students into scheduled classes.
DROP TABLE IF EXISTS `enrollments`;
CREATE TABLE `enrollments` (
  `enrollment_id` INT AUTO_INCREMENT,
  `student_id` INT NOT NULL,
  `class_id` INT NOT NULL,
  `enroll_status` VARCHAR(20) NOT NULL DEFAULT 'Active',
  PRIMARY KEY (`enrollment_id`),
  CONSTRAINT `fk_enrollments_students` 
    FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) 
    ON DELETE CASCADE,
  CONSTRAINT `fk_enrollments_classes` 
    FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) 
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. Table Name: grades
-- Purpose: Academic evaluation logs mapped directly onto specific class enrollment instances. Links 1:1 with enrollments.
DROP TABLE IF EXISTS `grades`;
CREATE TABLE `grades` (
  `grade_id` INT AUTO_INCREMENT,
  `enrollment_id` INT NOT NULL UNIQUE,
  `numeric_score` DECIMAL(5,2) NOT NULL,
  `letter_grade` VARCHAR(2) NOT NULL,
  PRIMARY KEY (`grade_id`),
  CONSTRAINT `fk_grades_enrollments` 
    FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`enrollment_id`) 
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;