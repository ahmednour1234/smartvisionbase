-- SmartVision CRM - Middle East countries only
-- Safe data change: no schema changes, just toggles is_active
-- Review before running.

UPDATE countries SET is_active=0;

UPDATE countries SET is_active=1 WHERE name IN (
  'Bahrain','Egypt','Iran','Iraq','Israel','Jordan','Kuwait','Lebanon','Oman',
  'Palestine','Qatar','Saudi Arabia','Syria','Turkey','United Arab Emirates','Yemen'
);
