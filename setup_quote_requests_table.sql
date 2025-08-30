-- Setup script for quote_requests table
-- Run this in your MySQL database to create the missing table

CREATE TABLE IF NOT EXISTS `quote_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `service_type` varchar(100) NOT NULL,
  `project_details` text NOT NULL,
  `status` enum('pending','reviewed','contacted','completed','cancelled') DEFAULT 'pending',
  `submission_date` timestamp DEFAULT CURRENT_TIMESTAMP,
  `admin_notes` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional: Add some sample data for testing
-- INSERT INTO quote_requests (full_name, email, phone, service_type, project_details) VALUES
-- ('John Doe', 'john@example.com', '123-456-7890', 'Wedding Photography', 'Need wedding photography for June 15th, 2024'),
-- ('Jane Smith', 'jane@example.com', '098-765-4321', 'Food Photography', 'Restaurant menu photography for new Italian restaurant');
