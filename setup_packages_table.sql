-- Create packages table if it doesn't exist
CREATE TABLE IF NOT EXISTS `packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `price` varchar(100) NOT NULL,
  `details` text NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_category` (`name`, `category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data for testing
INSERT IGNORE INTO `packages` (`name`, `category`, `price`, `details`) VALUES
('Bronze Package', 'Food Photography', '$299', 'Basic food photography package with 50 edited photos'),
('Silver Package', 'Food Photography', '$499', 'Standard food photography package with 100 edited photos'),
('Gold Package', 'Food Photography', '$799', 'Premium food photography package with 150 edited photos'),
('Bronze Package', 'Wedding Photography', '$599', 'Basic wedding photography package with 100 edited photos'),
('Silver Package', 'Wedding Photography', '$999', 'Standard wedding photography package with 200 edited photos'),
('Gold Package', 'Wedding Photography', '$1499', 'Premium wedding photography package with 300 edited photos'),
('Bronze Package', 'Hotel Photography', '$399', 'Basic hotel photography package with 75 edited photos'),
('Silver Package', 'Hotel Photography', '$699', 'Standard hotel photography package with 150 edited photos'),
('Gold Package', 'Hotel Photography', '$1099', 'Premium hotel photography package with 250 edited photos'),
('Bronze Package', 'Commercial Photography', '$499', 'Basic commercial photography package with 50 edited photos'),
('Silver Package', 'Commercial Photography', '$899', 'Standard commercial photography package with 100 edited photos'),
('Gold Package', 'Commercial Photography', '$1299', 'Premium commercial photography package with 200 edited photos'),
('Bronze Package', 'Model Photography', '$399', 'Basic model photography package with 50 edited photos'),
('Silver Package', 'Model Photography', '$699', 'Standard model photography package with 100 edited photos'),
('Gold Package', 'Model Photography', '$999', 'Premium model photography package with 150 edited photos'),
('Bronze Package', 'Event Photography', '$299', 'Basic event photography package with 50 edited photos'),
('Silver Package', 'Event Photography', '$599', 'Standard event photography package with 100 edited photos'),
('Gold Package', 'Event Photography', '$899', 'Premium event photography package with 150 edited photos'); 