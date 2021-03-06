CREATE TABLE `social_story_protocols` (
  `protocol_id` int(11) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `seizure_protocol_details` text,
  `seizure_upload` varchar(1000) DEFAULT NULL,
  `slip_fall_protocol_details` text,
  `slip_fall_upload` varchar(1000) DEFAULT NULL,
  `transfer_protocol_details` text,
  `transfer_upload` varchar(1000) DEFAULT NULL,
  `toileting_protocol_details` text,
  `toileting_upload` varchar(1000) DEFAULT NULL,
  `bathing_protocol_details` text,
  `bathing_upload` varchar(1000) DEFAULT NULL,
  `gtube_protocol_details` text,
  `gtube_upload` varchar(1000) DEFAULT NULL,
  `oxygen_protocol_details` text,
  `oxygen_upload` varchar(1000) DEFAULT NULL,
  `notes_protocol_details` text,
  `note_upload` varchar(1000) DEFAULT NULL
);