# Hall of Legends

Hall of Legends is a gaming achievement community where players post speedruns, boss kills, clips, and screenshots, earn reputation, and climb the leaderboard.

The app is built with PHP and MySQL, with one shared stylesheet system and page-specific styling for each screen.

## Project summary

- Users can register, log in, and log out.
- Users can create entries with a title, game, type, description, and optional media upload.
- Entries earn reputation based on type.
- Users can like entries and leave comments.
- A leaderboard ranks users by total reputation.
- Admins can moderate entries and promote or revoke admin access.
- Rank badges are displayed using symbol images for Bronze, Silver, Gold, Platinum, and Diamond.

## Tech stack

- PHP 8
- MySQL
- mysqli prepared statements
- Vanilla JavaScript
- Plain CSS

## File structure

- `index.php` — main feed
- `entry.php` — entry detail view
- `leaderboard.php` — leaderboard screen
- `post.php` — create an entry
- `edit.php` — edit an existing entry
- `delete.php` — delete an entry
- `like.php` — like an entry
- `comment.php` — add a comment
- `moderate.php` — admin moderation dashboard
- `promote.php` — grant/revoke admin access
- `login.php` / `register.php` — authentication screens
- `includes/functions.php` — shared helper functions
- `config/db.php` — database connection
- `database/hall_of_legends.sql` — schema and seeded data
- `assets/styles/` — CSS styling
- `uploads/` — uploaded media files

## Setup

1. Start Apache and MySQL in XAMPP.
2. Place the project in `htdocs/hall_of_legends`.
3. Open phpMyAdmin and import `database/hall_of_legends.sql`.
4. Confirm your MySQL credentials in `config/db.php` match your local setup.
5. Make sure the `uploads/` folder exists in the project root.
6. Open `http://localhost/hall_of_legends/register.php` to create an account.

## Demo accounts

All seeded accounts use the password: `password`

| Username | Role |
|---|---|
| admin_dragon | admin |
| mika.exe | user |
| jrunner_45 | user |
| 10noblade | user |
| coldstreak | user |

## How the app works

### 1. Authentication and users
Users sign up with a username, email, and password. Their role is stored in the `users` table, and reputation points are tracked there as well.

### 2. Posts and community feed
Entries are created through `post.php` and stored in `entries`. Each entry includes a game, type, description, and optional uploaded media file.

### 3. Engagement
Users can like and comment on entries. These actions are stored separately in `likes` and `comments`, which keeps the feed and ranking logic clean and scalable.

### 4. Ranking system
The `leaderboard_view` calculates each user's position based on reputation using a SQL window function. Rank badges update based on rep totals.

### 5. Administration
Admins can moderate entries and promote or revoke other admins through `moderate.php` and `promote.php`.

## Database features

The schema includes:

- `users` for authentication, role, reputation, and account metadata
- `entries` for game posts and media paths
- `likes` for likes on entries
- `comments` for user discussion
- `entry_feed_view` for feed data with live like count
- `leaderboard_view` for ranked user positions using `RANK()`

## ER diagram

![ER Diagram for Hall of Legends](ER%20Diagram%20for%20Hall%20of%20Legends.png)

## Notes

- All SQL queries use prepared statements.
- The app uses a shared dashboard style with strong game/community branding.
- Rank badges use custom image assets for Bronze, Silver, Gold, Platinum, and Diamond tiers.
- Entry detail pages show the uploaded media when present and a game-cover fallback when no upload exists.

## Video



## Attributions
[Magnific.com: Colored and isolated realistic diamond gemstone icon](https://www.magnific.com/free-vector/colored-isolated-realistic-diamond-gemstone-icon-set-round-shapes-different-colors_5084106.htm#fromView=search&page=1&position=5&uuid=c9ba584f-6d2c-408f-b536-d18908ec35f1&track=ais_hybrid&query=diamond)