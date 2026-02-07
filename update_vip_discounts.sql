-- Update VIP tier discount percentages
UPDATE vip_tiers SET discount_percentage = 2.00 WHERE rank = 1;
UPDATE vip_tiers SET discount_percentage = 4.00 WHERE rank = 2;
UPDATE vip_tiers SET discount_percentage = 6.00 WHERE rank = 3;
UPDATE vip_tiers SET discount_percentage = 8.00 WHERE rank = 4;
UPDATE vip_tiers SET discount_percentage = 10.00 WHERE rank = 5;

-- Verify the updates
SELECT rank, title_en, title_ar, discount_percentage, deposits_required 
FROM vip_tiers 
ORDER BY rank;
