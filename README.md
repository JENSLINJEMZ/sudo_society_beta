# 🛡️ Sudo Society Beta

> A complete web-based **Capture The Flag platform** for cybersecurity competitions, training programs, and challenge-based learning.

---

## 📌 Overview

**Sudo Society Beta** is a web-based **Capture The Flag (CTF)** platform developed using:

* PHP
* MySQL
* HTML
* CSS
* JavaScript

The platform provides a competitive cybersecurity environment where participants can create accounts, solve challenges, earn points, form teams, and compete for higher positions on the leaderboard.

It also includes a dedicated **admin panel** for managing users, challenges, platform activity, and overall system statistics.

Sudo Society Beta is suitable for:

* Cybersecurity events
* College technical competitions
* Ethical hacking workshops
* Security training sessions
* Challenge-based learning programs
* Internal CTF competitions

---

# 🚀 Core Features

## 👤 User Authentication

The platform provides a secure account management system where users can:

* Register a new account
* Log in using a username or email address
* Stay logged in using PHP sessions
* Log out securely
* Validate usernames, emails, and passwords
* Update account credentials

---

## 🚩 Challenge System

Participants can browse and solve cybersecurity challenges from different categories.

Each challenge may contain:

* Challenge title
* Category
* Description
* Difficulty level
* Point value
* Challenge link or downloadable resource
* Flag submission field

The system can:

* Display active challenges
* Check whether the current user has already solved a challenge
* Accept flag submissions
* Validate submitted flags
* Prevent duplicate solves
* Award points for correct answers
* Update the user's total score
* Increase the challenge solve count
* Record completed challenges

---

## 🏆 Leaderboard

The leaderboard displays participants based on their total score.

It allows users to:

* View player rankings
* Compare scores with other participants
* Track competition progress
* View ranking changes after solving challenges
* Identify top-performing players

Rankings are automatically updated whenever a participant successfully solves a challenge.

---

## 👥 Team Management

Participants can collaborate by creating or joining teams.

The team system supports:

* Creating a new team
* Joining an existing team
* Viewing team details
* Viewing the team leader
* Viewing team members
* Leaving a team
* Managing team membership
* Tracking team participation

This feature encourages teamwork and collaborative problem-solving during CTF events.

---

## ⚙️ Profile and Account Settings

Users can manage their personal information through the settings page.

Available options include:

* Update username
* Update email address
* Add or edit a bio
* Select or update country
* Change account password
* Upload a profile avatar
* View account statistics

Profile statistics may include:

* Total score
* Challenges solved
* Current rank
* Activity streak
* Team information
* Recent activity

---

## 🧑‍💻 Admin Dashboard

The project includes a dedicated administrative interface for managing the entire platform.

Administrators can:

* Log in through a protected admin area
* View total registered users
* View total challenges
* View total challenge solves
* View the combined platform score
* Manage user accounts
* Edit user information
* Manage challenge data
* Add, update, or remove challenges
* Recalculate rankings
* Review user and platform activity
* Monitor overall system performance

---

# ⚙️ How the System Works

## 1. Frontend Interface

The user interface is built using HTML, CSS, JavaScript, and PHP pages.

The major frontend pages include:

* Landing page
* User dashboard
* Challenge page
* Team page
* Leaderboard page
* Profile settings page
* Login and registration pages
* Event page
* Admin dashboard

JavaScript is used to communicate with backend API files and dynamically update page content.

---

## 2. Backend APIs

The PHP files inside the `api` folder act as backend endpoints.

These APIs:

* Receive requests from frontend pages
* Validate submitted data
* Connect to the MySQL database
* Perform requested operations
* Return JSON responses
* Update the interface dynamically

---

## 3. Database Layer

The project uses MySQL through PHP's `mysqli` extension.

The main database connection is handled by:

```text
lib/includes/Database.class.php
```

This class creates and manages the connection between the PHP backend and the MySQL database.

---

## 4. Session-Based Authentication

When a user successfully logs in, important account details are stored in the PHP session.

Common session values include:

* User ID
* Username
* Login status

The session is used to identify the currently logged-in user across different pages and API requests.

---

## 5. Challenge Submission Flow

The challenge-solving process follows these steps:

1. The user selects a challenge.
2. The user enters and submits a flag.
3. The backend receives the submitted flag.
4. The submitted flag is compared with the correct flag stored in the database.
5. The system checks whether the challenge has already been solved.
6. If the answer is correct, the solve is recorded.
7. The user's score is increased.
8. The challenge solve count is updated.
9. The leaderboard ranking is recalculated.

---

## 6. Team Flow

The team system works as follows:

1. A user creates a new team or selects an existing team.
2. Team information is stored in the database.
3. The user's account is linked to the selected team.
4. Team member information becomes visible on the team page.
5. Users can leave or manage their team membership.

---

## 7. Admin Flow

The admin management process includes:

1. The administrator logs in through the admin login page.
2. The admin session is validated.
3. Platform statistics are loaded from the database.
4. The administrator can manage users and challenges.
5. Administrative actions are performed through admin API endpoints.
6. Changes are reflected across the platform.

---

# 📁 Project File Structure

```text
sudo_society_beta/
│
├── index.html
├── dashboard.php
├── challenges.html
├── teams.html
├── settings.html
├── login.html
├── register.html
├── forgot-password.html
├── event.php
├── leaderboard.php
├── logout.php
│
├── admin/
│   ├── admin_login.php
│   ├── admin_dashboard.php
│   ├── admin_api.php
│   ├── challengapi.php
│   ├── admin.php
│   └── admin.html
│
├── api/
│   ├── login.php
│   ├── register.php
│   ├── challenge.php
│   ├── api.php
│   ├── teams.php
│   ├── settings.php
│   ├── task.php
│   ├── get_events.php
│   ├── leaderboard.php
│   └── test.php
│
├── lib/
│   ├── load.php
│   └── includes/
│       ├── Database.class.php
│       ├── Users.class.php
│       ├── validation.class.php
│       └── hash.class.php
│
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
│
└── Database/
    └── SQL and database-related files
```

---

# 📄 Root Files

## `index.html`

The main landing page of the Sudo Society platform.

## `dashboard.php`

Displays the logged-in user's account information, score, statistics, and progress.

## `challenges.html`

Displays available challenges and allows users to submit flags.

## `teams.html`

Handles team browsing, creation, joining, leaving, and member information.

## `settings.html`

Allows users to update their profile information, password, country, bio, and avatar.

## `login.html`

Provides the user login interface.

## `register.html`

Provides the user registration interface.

## `forgot-password.html`

Handles the password recovery interface.

## `event.php`

Displays event-related information.

## `leaderboard.php`

Displays the public player rankings.

## `logout.php`

Destroys the current user session and safely logs the user out.

---

# 🛠️ Admin Files

## `admin/admin_login.php`

Provides the administrator login interface.

## `admin/admin_dashboard.php`

The main admin dashboard used to monitor and manage the platform.

## `admin/admin_api.php`

Handles backend operations for:

* Platform statistics
* User management
* Challenge management
* Ranking updates
* Administrative actions

## `admin/challengapi.php`

Contains additional admin-side challenge management logic.

## `admin/admin.php`

Acts as an admin entry point or supporting admin page.

## `admin/admin.html`

Contains an additional administrative interface.

---

# 🔌 API Files

## `api/login.php`

Handles:

* Login requests
* Credential verification
* Session creation
* Login responses

## `api/register.php`

Handles:

* New user registration
* Username validation
* Email validation
* Password validation
* Account creation

## `api/challenge.php`

Handles:

* Challenge retrieval
* Flag submission
* Flag validation
* Solve recording
* Score updates

## `api/api.php`

Provides:

* Dashboard statistics
* User information
* Ranking data
* Leaderboard information
* User progress details

## `api/teams.php`

Handles:

* Team listings
* Team creation
* Team details
* Joining teams
* Leaving teams
* Team membership management

## `api/settings.php`

Handles:

* Profile updates
* Username and email changes
* Password changes
* Bio and country updates
* Avatar uploads

## `api/task.php`

Handles task-based challenge content and answer verification.

## `api/get_events.php`

Retrieves event-related data from the database.

## `api/leaderboard.php`

Provides leaderboard and ranking information.

## `api/test.php`

Used for testing, debugging, or experimental API functionality.

---

# 📚 Library Files

## `lib/includes/Database.class.php`

Handles the MySQL database connection.

## `lib/includes/Users.class.php`

Contains user-related logic and operations.

## `lib/includes/validation.class.php`

Provides reusable validation functions for submitted data.

## `lib/includes/hash.class.php`

Handles secure password hashing and password verification.

## `lib/load.php`

Loads shared classes, database connections, and reusable libraries.

---

# 🗃️ Database Structure

The project uses several major database tables.

## `users`

Stores:

* User account information
* Login credentials
* Score
* Rank
* Team information
* Account status

## `challenges`

Stores:

* Challenge title
* Description
* Category
* Correct flag
* Point value
* Challenge status
* Solve count

## `solved_challenges`

Stores records of challenges successfully solved by users.

## `users_datas`

Stores additional profile information such as:

* Bio
* Country
* Avatar
* Profile settings

## `teams`

Stores:

* Team name
* Team leader
* Team members
* Team-related information

## `achievements`

Stores user achievements and unlocked rewards.

## `activity_log`

Records user and administrator actions across the platform.

## `score_history`

Stores historical score changes for users.

## `ctf_tasks`

Stores task-based challenge content and answers.

## `events`

Stores cybersecurity event and competition information.

---

# 💻 System Requirements

To run Sudo Society Beta, the following software is required:

* Apache web server
* PHP 7.0 or newer
* MySQL or MariaDB
* XAMPP, WAMP, MAMP, or Laragon
* Modern web browser

Recommended environment:

* PHP 8 or newer
* MySQL 8 or newer
* Apache 2.4 or newer

---

# 🧰 Installation and Setup

## Step 1: Move the Project

Place the project folder inside your web server's document root.

### XAMPP

```text
C:\xampp\htdocs\sudo_society_beta
```

### Laragon

```text
C:\laragon\www\sudo_society_beta
```

---

## Step 2: Create the Database

Open phpMyAdmin or another MySQL management tool.

Create a new database for the project.

Example database name:

```text
sudo_society
```

---

## Step 3: Import Database Tables

Import the SQL file available inside the `Database` folder.

Ensure that all required tables are successfully created.

---

## Step 4: Configure Database Credentials

Open:

```text
lib/includes/Database.class.php
```

Update the database configuration with your local credentials.

Example configuration:

```php
private $host = "localhost";
private $username = "root";
private $password = "";
private $database = "sudo_society";
```

---

## Step 5: Start the Services

Start:

* Apache
* MySQL

These services can be started using the XAMPP, WAMP, or Laragon control panel.

---

## Step 6: Open the Platform

Open the following URL in your browser:

```text
http://localhost/sudo_society_beta/
```

---

# 🌐 Application URLs

| Page        | Local URL                                                  |
| ----------- | ---------------------------------------------------------- |
| Home        | `http://localhost/sudo_society_beta/`                      |
| Login       | `http://localhost/sudo_society_beta/login.html`            |
| Register    | `http://localhost/sudo_society_beta/register.html`         |
| Dashboard   | `http://localhost/sudo_society_beta/dashboard.php`         |
| Challenges  | `http://localhost/sudo_society_beta/challenges.html`       |
| Teams       | `http://localhost/sudo_society_beta/teams.html`            |
| Settings    | `http://localhost/sudo_society_beta/settings.html`         |
| Leaderboard | `http://localhost/sudo_society_beta/leaderboard.php`       |
| Admin Login | `http://localhost/sudo_society_beta/admin/admin_login.php` |

---

# 🔐 Security Considerations

Before deploying the platform publicly, the following improvements are recommended:

* Use strong administrator authentication
* Store admin credentials in the database
* Use `password_hash()` and `password_verify()`
* Regenerate session IDs after login
* Add CSRF protection
* Validate all user input
* Use prepared SQL statements
* Restrict avatar file types and file sizes
* Prevent executable file uploads
* Add rate limiting for login and flag submissions
* Disable debug output in production
* Protect sensitive API endpoints
* Add role-based access control
* Store secrets in environment configuration files
* Use HTTPS in production

---

# 🧹 Development Notes

Sudo Society is currently in the **beta and active development stage**.

Some areas may require further improvement:

* Certain backend files have overlapping functionality.
* API naming can be made more consistent.
* Database column names should follow a single naming convention.
* Admin authentication should be strengthened.
* Test and debugging files should be removed before production.
* Debug logs should be disabled in production.
* Error messages should avoid exposing sensitive information.
* Repeated code can be moved into reusable functions or classes.
* API responses should use a consistent JSON structure.

---

# 🗺️ Future Improvements

Possible future enhancements include:

* Email verification
* Password reset using email tokens
* Two-factor authentication
* Real-time leaderboard updates
* Team-based scoring
* Challenge hints with point deductions
* Challenge difficulty levels
* Achievement badges
* User activity timelines
* Event registration
* Competition start and end timers
* Challenge file downloads
* Docker-based challenge deployment
* Dynamic flag generation
* Admin activity monitoring
* User banning and suspension
* API rate limiting
* Mobile-responsive admin dashboard
* Dark and light themes
* Notifications
* Competition announcements

---

# 📊 Project Summary

**Sudo Society Beta** is a PHP and MySQL-based Capture The Flag platform that provides:

* Secure user registration and login
* Cybersecurity challenge solving
* Automatic scoring
* Dynamic leaderboard rankings
* Team creation and collaboration
* Profile and avatar management
* Event-related functionality
* Administrator management tools
* Activity and score tracking

The platform is designed to provide an engaging environment for cybersecurity learning, competitions, workshops, and technical events.

---

## ⚠️ Project Status

```text
Status: Beta
Development: Active
Deployment: Local or private testing recommended
Production readiness: Requires additional security hardening
```

---

## 🛡️ Sudo Society

**Learn. Exploit. Defend. Dominate.**

> Build your skills, capture the flags, and rise to the top.
