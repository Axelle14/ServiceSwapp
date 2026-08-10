-- Migration: Add location fields to services
-- Run once after initial schema setup.
ALTER TABLE services
  ADD COLUMN location_address VARCHAR(500) NULL AFTER credits,
  ADD COLUMN latitude DECIMAL(10,8) NULL AFTER location_address,
  ADD COLUMN longitude DECIMAL(11,8) NULL AFTER latitude;
