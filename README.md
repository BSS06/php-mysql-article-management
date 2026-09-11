# Article Management & Editorial System (MySQL & PHP Distributed Architecture)

A multi-user relational database management system (RDBMS) designed for managing editorial content, user identities, permissions, and reader interactions. Built with a distributed 3-node database architecture on **MySQL / MariaDB**, a **PHP** back-end API, and an interactive **Single Page Application (SPA)** front-end featuring dynamic data analytics and administrative **CRUD** capabilities.

---

## 📌 Features & Highlights

- **Multi-Node Architecture**: Scalable 3-database separation separating core domains:
  - `DB1 (gestiune_utilizatori)`: Identity, authentication, sessions, and Role-Based Access Control (RBAC).
  - `DB2 (gestiune_continut)`: Articles, categories, tags, and editorial version auditing.
  - `DB3 (gestiune_interactiuni)`: Comments, ratings, view counters, daily analytics, and notifications.
- **Role-Based Access Control (RBAC)**: Distinct permissions for **Administrator**, **Editor/Author**, and **Reader**:
  - **Administrators**: Full system control, user account management, categories, moderation, and integrity testing.
  - **Editors / Authors**: Draft creation, article editing, version control, and managing own articles.
  - **Readers**: Public content browsing, commenting (nested thread support), and rating articles.
- **Analytical Objectives & KPI Dashboard**: 14 predefined SQL queries providing analytical insights, rendered as interactive tables and graphical charts (**Chart.js**). Includes a real-time KPI overview card section.
- **Cross-Node Queries**: Seamless cross-database joins leveraging `database.table` syntax and scalar subqueries for holistic reporting across all 3 nodes.
- **Full CRUD Support**: Complete Create, Read, Update, and Delete operations using prepared SQL statements for enhanced security against SQL injection.
- **OOP PHP Integration**: Includes Object-Oriented PHP logic implementing encapsulation, inheritance, polymorphism, and automated data validation.
- **Live Metadata & Dynamic Schema Documentation**: Uses MySQL `INFORMATION_SCHEMA` alongside native `JSON_OBJECT()` and `GROUP_CONCAT()` functions to generate live JSON database schema documentation.
- **Multi-User Data Integrity Testing**: Built-in test suite verifying ACID properties under concurrent operations, primary/foreign key constraints, and multi-user isolation.

---

## 🏗 System Architecture

The project decouples concerns by separating identity, content, and analytics into three dedicated, autonomous nodes:

```
                  ┌──────────────────────────────────────────┐
                  │          PHP API Layer & SPA             │
                  └─────┬──────────────────┬───────────┬─────┘
                        │                  │           │
       ┌────────────────┴──────┐   ┌───────┴────────┐  └────────────────┐
       ▼                       ▼                    ▼                   ▼
┌─────────────────────────┐  ┌────────────────────────┐  ┌────────────────────────────┐
│          DB1            │  │          DB2           │  │            DB3             │
│   gestiune_utilizatori  │  │    gestiune_continut   │  │   gestiune_interactiuni    │
├─────────────────────────┤  ├────────────────────────┤  ├────────────────────────────┤
│ • roluri                │  │ • categorii            │  │ • comentarii               │
│ • utilizatori           │  │ • articole             │  │ • evaluari                 │
│ • permisiuni            │  │ • etichete             │  │ • vizualizari              │
│ • sesiuni               │  │ • articole_etichete    │  │ • notificari               │
│                         │  │ • versiuni_articole    │  │ • statistici_zilnice       │
└─────────────────────────┘  └────────────────────────┘  └────────────────────────────┘
```

---

## 🛠 Tech Stack

- **Database Engine**: MySQL / MariaDB (InnoDB Storage Engine with transaction support and foreign key constraints).
- **Back-End**: PHP 8.x (Using native `mysqli` driver, session management, and prepared statements).
- **Front-End**: HTML5, CSS3, JavaScript (ES6+ AJAX fetch calls, Single Page Application layout).
- **Data Visualization**: Chart.js for real-time graphical metrics (pie charts, bar charts, line graphs).
- **Local Environment**: XAMPP / WAMP / LAMP stack (Apache web server + MySQL/MariaDB).

---

## 📂 Database Schema Overview

### 1. `gestiune_utilizatori` (DB1)
- `roluri`: Stores user roles (`administrator`, `redactor`, `cititor`).
- `utilizatori`: System user accounts, SHA-256 hashed passwords, and account status.
- `permisiuni`: Granular mapping of roles to permitted actions.
- `sesiuni`: Active user session tokens and expiration timestamps.

### 2. `gestiune_continut` (DB2)
- `categorii`: Thematic categories with optional hierarchical parenting (`categorie_parinte`).
- `articole`: Core editorial content with status tracking (`draft`, `publicat`, `arhivat`).
- `etichete`: Tag nomenclature for flexible categorization.
- `articole_etichete`: Many-to-Many junction table linking articles to tags.
- `versiuni_articole`: Audit log tracking article revisions, timestamps, and editor IDs.

### 3. `gestiune_interactiuni` (DB3)
- `comentarii`: Reader comments with hierarchical threading (`comentariu_parinte`) and moderation status.
- `evaluari`: Numerical rating scores (1-5 star constraint) per user/article pair.
- `vizualizari`: Detailed view logs (IP, user agent, duration).
- `notificari`: User notifications system for updates and replies.
- `statistici_zilnice`: Aggregated daily engagement metrics.

---

## 📊 Analytical Objectives (O1 - O14)

1. **O1 (DB1)**: Active users list sorted by assigned roles.
2. **O2 (DB1)**: User distribution breakdown per role (Pie Chart).
3. **O3 (DB1)**: Identification of inactive or unauthenticated user accounts for security cleanup.
4. **O4 (DB2)**: Inventory of published articles mapped to categories.
5. **O5 (DB2)**: Article density distribution per category (Bar Chart).
6. **O6 (DB2)**: Top 3 most viewed articles overall.
7. **O7 (DB2)**: Identification of draft articles and responsible editors to spot pipeline bottlenecks.
8. **O8 (DB3)**: Average reader ratings per article (1-5 Scale Bar Chart).
9. **O9 (DB3)**: Active comment discussion threads with reply counts.
10. **O10 (DB3)**: Daily reader traffic trend over the last 7 days (Line Chart).
11. **O11 (DB1 × DB2)**: Cross-node author productivity ranking (articles published vs. views generated).
12. **O12 (DB2 × DB3)**: Popular articles analyzing approved comments and average review scores.
13. **O13 (DB1 × DB3)**: Top engaged readers sorted by comment and evaluation activity.
14. **O14 (DB1 × DB2 × DB3)**: Unified cross-node KPI Executive Dashboard.

---

## 🚀 Installation & Setup

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (or any Apache + MySQL + PHP server environment).

### 1. Repository Setup
Clone or place the project files inside your web server's document root (e.g., `C:/xampp/htdocs/gestiune_articole_crud/`):
```bash
git clone https://github.com/BrSerghei/MYSQL_GESTIUNE_MANAGEMENT_ARTICOLE.git
```

### 2. Database Import
1. Start **Apache** and **MySQL** modules from the XAMPP Control Panel.
2. Open phpMyAdmin at `http://localhost/phpmyadmin/`.
3. Create the three databases with `utf8mb4` encoding:
   ```sql
   CREATE DATABASE gestiune_utilizatori CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE DATABASE gestiune_continut CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE DATABASE gestiune_interactiuni CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
4. Execute the schema and seed scripts provided in the project repository for each database.

### 3. Application Configuration
Verify database parameters in `config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Set your MySQL password if applicable
```

### 4. Running the Application
Access the portal in your browser:
```
http://localhost/gestiune_articole_crud/
```

---

## 🔒 Security Measures

- **SQL Injection Defense**: All user inputs in CRUD and authentication pipelines are parameterized using PHP Prepared Statements (`mysqli_stmt`).
- **Password Security**: Passwords are hashed using `SHA2(password, 256)` in MySQL or equivalent SHA-256 server-side routines.
- **Session Protection**: Active session validation using secure tokens (`session_regenerate_id()`) to prevent session fixation and hijacking.
- **Role Isolation**: Back-end endpoints enforce strict privilege checks before processing CRUD actions.
