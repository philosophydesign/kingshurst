--
-- Table structure for table `regint_fields`
--

CREATE TABLE `regint_fields` (
  `id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `ref` varchar(255) NOT NULL,
  `mandatory` tinyint(1) NOT NULL,
  `val` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `options` tinytext NOT NULL,
  `readonly` tinyint(1) NOT NULL,
  `rowindex` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `regint_forms`
--

CREATE TABLE `regint_forms` (
  `id` int(11) NOT NULL,
  `reference` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `regint_submissions`
--

CREATE TABLE `regint_submissions` (
  `id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `post_id` int(11) NOT NULL,
  `email` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `regint_submissions_data`
--

CREATE TABLE `regint_submissions_data` (
  `id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `regint_fields`
--
ALTER TABLE `regint_fields`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `regint_forms`
--
ALTER TABLE `regint_forms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `regint_submissions`
--
ALTER TABLE `regint_submissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `regint_submissions_data`
--
ALTER TABLE `regint_submissions_data`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `regint_fields`
--
ALTER TABLE `regint_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `regint_forms`
--
ALTER TABLE `regint_forms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `regint_submissions`
--
ALTER TABLE `regint_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `regint_submissions_data`
--
ALTER TABLE `regint_submissions_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;