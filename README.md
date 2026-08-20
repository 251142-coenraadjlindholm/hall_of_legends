# Hall of Legends

Hall of Legends is a gaming achievement community where players post speedruns, boss kills, clips, and screenshots, earn reputation, and climb the leaderboard.

The app is built with PHP and MySQL, with one shared stylesheet system and page-specific styling for each screen.

## Project summary

- Users can register, log in, and log out.
- Users can create entries with a title, game, type, description, and optional media upload.
- Entries earn reputation based on type.
- Users can like entries and leave comments.
- A leaderboard ranks users by active reputation, with 50 rep lost for each day without a new post.
- Admins can moderate entries and promote or revoke admin access.
- Rank badges are displayed using symbol images for Bronze, Silver, Gold, Platinum, and Diamond.

## Tech stack

- PHP 8
- MySQL
- mysqli prepared statements
- Vanilla JavaScript
- Plain CSS

## Project structure

```text
hall_of_legends/
|-- assets/
|   |-- js/
|   |   `-- main.js
|   `-- styles/
|       |-- base.css
|       |-- entry.css
|       |-- index.css
|       |-- leaderboard.css
|       |-- login.css
|       |-- moderate.css
|       |-- post.css
|       `-- register.css
|-- config/
|   `-- db.php
|-- database/
|   |-- hall_of_legends.sql
|   `-- upgrade_legend_score_decay.sql
|-- includes/
|   |-- footer.php
|   |-- functions.php
|   |-- header.php
|   `-- social-icons.php
|-- uploads/
|   `-- .gitattributes
|-- index.php
|-- entry.php
|-- leaderboard.php
|-- post.php
|-- edit.php
|-- delete.php
|-- like.php
|-- comment.php
|-- moderate.php
|-- promote.php
|-- login.php
|-- logout.php
|-- register.php
|-- Bronze.png / Silver.png / Gold.png
|-- Platinum.png / Diamond.png
|-- Celeste.jpg / Hades.jpg / Hollowknight.jpg / Melania.jpg
|-- ER Diagram for Hall of Legends.png
`-- README.md
```

### Main folders

- `assets/` contains the shared JavaScript and page-specific CSS files.
- `config/` contains the database connection settings.
- `database/` contains the schema, seed data, and database upgrade script.
- `includes/` contains shared layout components and helper functions.
- `uploads/` stores user-uploaded media files.

### Main PHP pages

- `index.php` displays the main feed and `entry.php` displays an individual entry.
- `post.php`, `edit.php`, and `delete.php` manage entry content.
- `like.php` and `comment.php` handle engagement actions.
- `leaderboard.php` displays rankings and reputation.
- `login.php`, `register.php`, and `logout.php` handle authentication.
- `moderate.php` and `promote.php` provide administrator controls.

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
The `leaderboard_view` calculates each user's position using a SQL window function. Active reputation loses 50 points per day after the user's latest post, with a floor of zero, so inactivity can lower their rank. Users who have never posted do not lose their starting reputation.

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
- For an existing database, run `database/upgrade_legend_score_decay.sql` once in phpMyAdmin to replace the old leaderboard view.
- The app uses a shared dashboard style with strong game/community branding.
- Rank badges use custom image assets for Bronze, Silver, Gold, Platinum, and Diamond tiers.
- Entry detail pages show the uploaded media when present and a game-cover fallback when no upload exists.
- The animated background continuously layers moving waves, light sweeps, scan lines, grain, and a vignette to create a live game-themed atmosphere.
- A soft radial highlight follows the cursor across the background while the pointer is active, then fades out when it leaves the page.

## Video

[Watch the Hall of Legends Demo Video](https://drive.google.com/file/d/1JQB_M3Qg9sqaiMlJvpD82MbJNeyr5cBc/view?usp=sharing)


## Attributions
[Magnific.com: Colored and isolated realistic diamond gemstone icon](https://www.magnific.com/free-vector/colored-isolated-realistic-diamond-gemstone-icon-set-round-shapes-different-colors_5084106.htm#fromView=search&page=1&position=5&uuid=c9ba584f-6d2c-408f-b536-d18908ec35f1&track=ais_hybrid&query=diamond)
- `cards.png` image asset