-- user roles
INSERT INTO 
  `user_roles` (`name`, `created_at`) 
VALUES 
  ('superadmin', current_timestamp());

-- account status
INSERT INTO 
  `account_status` (`name`, `created_at`) 
VALUES 
  ('sold', current_timestamp()), 
  ('pending', current_timestamp()), 
  ('unpend', current_timestamp()),
  ('retrieved', current_timestamp());