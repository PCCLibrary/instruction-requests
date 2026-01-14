-- Academic Terms for 2025-2026
-- Generated: 2026-01-14
-- Run with: mysql -u username -p database_name < database/sql/academic_terms_2025_2026.sql
-- Or via tinker: DB::unprepared(file_get_contents(database_path('sql/academic_terms_2025_2026.sql')));

INSERT INTO academic_terms (academic_year, term_name, start_date, end_date, sort_order, created_at, updated_at) VALUES
('2025-2026', 'fall',   '2025-09-22', '2025-12-19', 1, NOW(), NOW()),
('2025-2026', 'winter', '2026-01-05', '2026-03-20', 2, NOW(), NOW()),
('2025-2026', 'spring', '2026-03-23', '2026-06-12', 3, NOW(), NOW()),
('2025-2026', 'summer', '2026-06-15', '2026-08-21', 4, NOW(), NOW());
