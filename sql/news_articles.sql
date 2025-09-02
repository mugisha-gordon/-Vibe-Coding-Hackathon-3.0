-- Create news_articles table
CREATE TABLE IF NOT EXISTS `news_articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `excerpt` text NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `author` varchar(100) NOT NULL,
  `date_published` datetime NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  KEY `date_published` (`date_published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample news articles
INSERT INTO `news_articles` (`title`, `content`, `excerpt`, `image_path`, `category`, `tags`, `author`, `date_published`) VALUES
('New School Building Inaugurated', '<p>We are thrilled to announce the inauguration of our new school building in Bumbobi village. This state-of-the-art facility will provide quality education to over 200 children in the community.</p><p>The new building features:</p><ul><li>6 modern classrooms</li><li>Computer lab</li><li>Library</li><li>Science laboratory</li><li>Sports facilities</li></ul><p>This achievement was made possible through the generous support of our donors and the hard work of our dedicated team.</p>', 'A new school building has been inaugurated in Bumbobi village, providing quality education facilities for over 200 children.', 'assets/images/news/school-building.jpg', 'Education', 'education, infrastructure, school', 'John Doe', '2024-03-15 10:00:00'),

('Medical Camp Success', '<p>Our recent medical camp in Bumbobi village was a tremendous success, providing free healthcare services to over 500 community members.</p><p>The medical camp offered:</p><ul><li>General health check-ups</li><li>Dental care</li><li>Eye examinations</li><li>Vaccinations</li><li>Health education sessions</li></ul><p>We would like to thank all the medical professionals who volunteered their time and expertise to make this possible.</p>', 'A successful medical camp provided free healthcare services to over 500 community members in Bumbobi village.', 'assets/images/news/medical-camp.jpg', 'Healthcare', 'healthcare, medical camp, community', 'Jane Smith', '2024-03-10 14:30:00'),

('Water Project Completion', '<p>We are proud to announce the completion of our water project, bringing clean drinking water to Bumbobi village. The project includes:</p><ul><li>New borehole installation</li><li>Water storage tanks</li><li>Distribution network</li><li>Water quality monitoring system</li></ul><p>This project will significantly improve the health and well-being of the community by providing access to clean water.</p>', 'A new water project brings clean drinking water to Bumbobi village, improving community health and well-being.', 'assets/images/news/water-project.jpg', 'Infrastructure', 'water, infrastructure, community development', 'Michael Johnson', '2024-03-05 09:15:00'),

('Youth Skills Training Program', '<p>Our youth skills training program has successfully trained 50 young people in various vocational skills. The program covered:</p><ul><li>Basic computer skills</li><li>Tailoring and fashion design</li><li>Woodworking</li><li>Electrical installation</li><li>Business management</li></ul><p>Many graduates have already started their own businesses or found employment in the local market.</p>', '50 young people complete vocational skills training program, gaining valuable skills for employment and entrepreneurship.', 'assets/images/news/youth-training.jpg', 'Education', 'youth, training, skills development', 'Sarah Williams', '2024-02-28 11:45:00'),

('Community Garden Initiative', '<p>Our community garden initiative has transformed an unused plot of land into a productive vegetable garden. The project:</p><ul><li>Provides fresh vegetables for the community</li><li>Creates employment opportunities</li><li>Promotes sustainable agriculture</li><li>Improves food security</li></ul><p>The garden is managed by local community members who have received training in modern farming techniques.</p>', 'A new community garden initiative promotes sustainable agriculture and improves food security in Bumbobi village.', 'assets/images/news/community-garden.jpg', 'Agriculture', 'agriculture, community garden, sustainability', 'David Brown', '2024-02-20 15:30:00'); 