-- Run this SQL on your production database to mark the migration as completed
-- This assumes the column already exists from the failed migration

INSERT INTO migrations (migration, batch) 
VALUES ('2026_02_05_233054_add_discount_percentage_to_vip_tiers_table', 
        (SELECT MAX(batch) FROM (SELECT batch FROM migrations) as m));

-- Then update the discount percentages if they haven't been set
UPDATE vip_tiers SET discount_percentage = rank * 2 WHERE discount_percentage = 0;
