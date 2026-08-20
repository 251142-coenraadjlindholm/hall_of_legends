USE hall_of_legends;

-- Replace the original leaderboard view with active reputation scoring.
-- Active reputation loses 50 points per day without a new entry and never drops below zero.
CREATE OR REPLACE VIEW leaderboard_view AS
SELECT
    active_users.user_id,
    active_users.username,
    active_users.rep,
    RANK() OVER (ORDER BY active_users.rep DESC) AS position
FROM (
    SELECT
        u.user_id,
        u.username,
        GREATEST(
            0,
            u.rep - CASE
                WHEN activity.last_post_at IS NULL THEN 0
                ELSE DATEDIFF(CURDATE(), DATE(activity.last_post_at)) * 50
            END
        ) AS rep
    FROM users u
    LEFT JOIN (
        SELECT user_id, MAX(created_at) AS last_post_at
        FROM entries
        GROUP BY user_id
    ) activity ON activity.user_id = u.user_id
) active_users;
