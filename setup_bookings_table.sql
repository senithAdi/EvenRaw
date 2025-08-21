-- Create bookings table if it doesn't exist
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `service_type` varchar(100) NOT NULL,
  `booking_date` date NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `event_location` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `booking_status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `special_requirements` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `booking_date` (`booking_date`),
  KEY `payment_status` (`payment_status`),
  KEY `booking_status` (`booking_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data for testing
INSERT IGNORE INTO `bookings` (`customer_id`, `service_type`, `booking_date`, `event_date`, `event_time`, `event_location`, `total_amount`, `payment_status`, `booking_status`, `special_requirements`) VALUES
(1, 'Wedding Photography', '2025-01-15', '2025-07-20', '10:00:00', 'Grand Hotel, Downtown', 1499.00, 'paid', 'confirmed', 'Outdoor ceremony, indoor reception'),
(1, 'Food Photography', '2025-01-16', '2025-07-20', '14:00:00', 'Restaurant ABC', 499.00, 'pending', 'pending', 'Menu photography for new restaurant'),
(1, 'Hotel Photography', '2025-01-17', '2025-07-20', '09:00:00', 'Luxury Hotel XYZ', 699.00, 'paid', 'confirmed', 'Hotel interior and exterior shots'),
(1, 'Commercial Photography', '2025-01-18', '2025-07-20', '11:00:00', 'Office Building', 899.00, 'paid', 'confirmed', 'Corporate headshots and office environment'),
(1, 'Event Photography', '2025-01-19', '2025-07-20', '16:00:00', 'Community Center', 599.00, 'pending', 'pending', 'Birthday party photography'),
(1, 'Model Photography', '2025-01-20', '2025-07-20', '13:00:00', 'Studio A', 699.00, 'failed', 'cancelled', 'Fashion portfolio shoot'),
(1, 'Wedding Photography', '2025-01-21', '2025-07-20', '12:00:00', 'Beach Resort', 1299.00, 'paid', 'confirmed', 'Beach wedding ceremony'),
(1, 'Food Photography', '2025-01-22', '2025-07-20', '15:00:00', 'Cafe Downtown', 299.00, 'pending', 'pending', 'Cafe menu photography'),
(1, 'Commercial Photography', '2025-01-23', '2025-07-20', '10:30:00', 'Shopping Mall', 799.00, 'paid', 'confirmed', 'Product photography for retail stores');

-- Add foreign key constraint if users table exists
-- ALTER TABLE `bookings` ADD CONSTRAINT `fk_bookings_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE; 