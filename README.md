# HelpDesk Ticket System

> **Developed by [@jfabrizzio](https://github.com/jfabrizzio) | CometaxCompany**

A full-featured helpdesk and ticket management system built with pure PHP 8+, MySQL, and Tailwind CSS.

---

## Features

- **Session-based authentication** with role management (admin, dev, user)
- **Ticket system** — create, view, assign, update status & phase, add internal notes, upload evidence files
- **Knowledge Base** — create and search documentation, templates, plans, and repository references
- **Weekly Plans** — track weekly progress by project with task checklists and progress indicators
- **Admin panel** — manage users (create / delete)
- **File uploads** served securely via PHP (not direct URLs)
- **Flash messages** and Post/Redirect/Get pattern throughout
- **Dark UI** with Tailwind CSS via CDN

## Tech Stack

- PHP 8.0+
- MySQL
- PDO with prepared statements
- Composer (PSR-4 autoload + `vlucas/phpdotenv`)
- Tailwind CSS (CDN)
- Apache `.htaccess` URL rewriting
- MVC architecture: Controllers → Services → Repositories → Models

## Project Structure

```
helpdesk/
├── public/               # Web root (index.php, .htaccess, uploads/)
│   ├── index.php         # Front controller / entry point
│   └── .htaccess
├── src/
│   ├── Core/             # Database, Router, Request, Response, Session
│   ├── Controllers/      # AuthController, TicketController, AdminController, ...
│   ├── Services/         # AuthService, TicketService, EvidenceService, ...
│   ├── Repositories/     # UserRepository, TicketRepository, ...
│   ├── Models/           # User, Ticket, Evidence, KnowledgeBase, WeeklyPlan
│   └── Middleware/       # AuthMiddleware, RoleMiddleware
├── views/
│   ├── auth/             # login.php
│   ├── tickets/          # list.php, create.php, detail.php
│   ├── admin/            # users.php, users_create.php
│   ├── knowledge/        # index.php, create.php, show.php
│   ├── weekly/           # index.php, create.php, show.php
│   └── partials/         # sidebar.php, header.php, flash.php
├── schema.sql            # Database schema + seed admin user
├── composer.json
├── .env.example
└── .env
```

## Installation

### Requirements

- PHP 8.0+
- MySQL 5.7+ or MariaDB 10.3+
- Apache with `mod_rewrite` enabled
- Composer

### Steps

1. **Clone the repository:**
   ```bash
   git clone https://github.com/CometaxCompany/helpdesk.git
   cd helpdesk
   ```

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Configure environment:**
   ```bash
   cp .env.example .env
   ```
   Edit `.env` to set your database credentials and `APP_URL`:
   ```env
   DB_HOST=localhost
   DB_NAME=helpdesk
   DB_USER=root
   DB_PASS=your_password
   APP_URL=http://localhost/helpdesk/public
   ```

4. **Create the database:**
   ```bash
   mysql -u root -p < schema.sql
   ```

5. **Set permissions:**
   ```bash
   chmod -R 755 public/uploads/
   ```

6. **Configure your web server** to point to the `public/` directory, or use Apache with the provided `.htaccess`.

7. **Access the application:**
   Open `http://localhost/helpdesk/public` in your browser.

### Default Admin Credentials

| Field    | Value                    |
|----------|--------------------------|
| Email    | admin@helpdesk.com       |
| Password | password                 |

> ⚠️ **Change the admin password immediately after first login.**

## User Roles

| Role  | Permissions |
|-------|-------------|
| admin | Full access — manage users, tickets, KB, weekly plans, assign devs |
| dev   | View all tickets, update status/phase, add notes, manage weekly plans |
| user  | Create tickets, view own tickets only |

## Routes

| Method | Path | Description |
|--------|------|-------------|
| GET | `/login` | Login page |
| POST | `/login` | Process login |
| GET | `/logout` | Logout |
| GET | `/tickets/list` | List tickets |
| GET | `/tickets/create` | Create ticket form |
| POST | `/tickets/store` | Submit ticket |
| GET | `/tickets/{id}` | Ticket detail |
| POST | `/tickets/{id}/status` | Update status/phase |
| POST | `/tickets/{id}/note` | Add note |
| POST | `/tickets/{id}/assign` | Assign developer |
| GET | `/tickets/{id}/evidence/{evidenceId}` | Download evidence file |
| GET | `/admin/users` | List users |
| GET | `/admin/users/create` | Create user form |
| POST | `/admin/users/store` | Create user |
| POST | `/admin/users/{id}/delete` | Delete user |
| GET | `/knowledge` | Knowledge base index |
| GET | `/knowledge/search?q=` | Search articles |
| GET | `/knowledge/create` | Create article form |
| POST | `/knowledge/store` | Create article |
| GET | `/knowledge/{id}` | View article |
| POST | `/knowledge/{id}/delete` | Delete article |
| GET | `/weekly-plan` | List weekly plans |
| GET | `/weekly-plan/create` | Create plan form |
| POST | `/weekly-plan/store` | Create plan |
| GET | `/weekly-plan/{id}` | View plan |
| POST | `/weekly-plan/{id}/task` | Add task |
| POST | `/weekly-plan/task/toggle` | Toggle task status |
| POST | `/weekly-plan/{id}/delete` | Delete plan |

## File Upload Security

Uploaded evidence files are stored outside the web-accessible path and served exclusively through PHP controllers that verify authentication and ownership before streaming files to the browser.

Allowed evidence types: `png`, `jpg`, `jpeg`, `pdf`, `xml`, `zip`, `mp4`

---

*HelpDesk · CometaxCompany · Developed by [@jfabrizzio](https://github.com/jfabrizzio)*
