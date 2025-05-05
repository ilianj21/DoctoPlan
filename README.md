# Task Management App (Symfony)

A collaborative **web application for project, task, and risk management**, built using the Symfony framework. This tool is designed to help teams clearly define responsibilities, monitor progress, and handle project risks efficiently in both professional and educational environments.

---

## ✨ Features Overview

### 📊 Project Management

* Create, edit, and archive projects
* Visual dashboard for project overview

### ✍️ Task Management

* Create, assign, and track tasks with statuses: **To do**, **In progress**, **Done**
* Multiple views: list, Kanban, and calendar

### ⚠️ Risk Management

* Identify and describe project risks
* Evaluate criticality and probability
* Define and track corrective actions

### 👥 Role & Permission System

* User roles: **Project Manager**, **Analyst**, **Developer**, **Tester**
* Role-based access control for secure operations

### ⏰ Notifications

* Automatic alerts on key events: task assignment, deadlines, updates

### 📊 Reports & Statistics

* Auto-generated reports
* Progress tracking, delays, and risk statistics

---

## 🔝 Non-Functional Requirements

### ⚡ Performance

* Page load under 2 seconds
* Supports up to 50 concurrent users

### 🔒 Security

* Strong authentication and access control
* Protection against CSRF, SQL injection, and XSS

### ⏺ Availability

* 99% uptime required from 08:00 to 20:00

### 🛍 UX & Ergonomics

* Responsive design
* Smooth and intuitive navigation

### ⚙ Maintainability

* Clean, modular Symfony code
* Easily extendable architecture

---

## 🧰 Technical Stack

* **Backend**: Symfony (latest stable)
* **Database**: MySQL or PostgreSQL
* **Frontend**: HTML5, CSS3 (Bootstrap allowed), JavaScript (or Vue.js/React)
* **Version control**: Git (hosted on GitHub or GitLab)

---

## ⚖ External Integrations (Optional)

* OAuth2 login via Google or Microsoft
* Email services: Mailgun, SendGrid via SMTP
* Import/export via APIs: Jira, Trello

---

## 🔢 Development & Testing

### Local Setup

```bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
symfony server:start
```

### Running Tests

```bash
vendor/bin/phpunit --configuration phpunit.xml
```

---

## 🔹 CI/CD Pipelines

* PHPStan and PHPUnit run on every branch (via GitHub Actions)
* Docker image build is manual and restricted to main branch only

---

## ✅ Validation Criteria

* All functional features implemented
* Symfony best practices followed (MVC, services, clear entities)
* Compliant with defined security standards
* Passing unit and functional tests

---

## 🚀 Getting Started for New Developers

1. Clone the repo
2. Set up `.env.local` for database and mail settings
3. Run `composer install`
4. Launch local server with Symfony CLI
5. Enjoy coding ✨

---

## 🚧 Roadmap Ideas (Post-MVP)

* Integration with project planning tools (e.g. Gantt charts)
* Time tracking features
* Public sharing of project boards