# PurFurredPetPilot

**PurFurredPetPilot** is an AI-driven pet care management application built with Laravel and Laravel Sail. It helps pet owners manage and monitor their pets' health, activities, and appointments in one convenient place.

## 🚀 Features

- **AI-Powered Insights** – Analyze pet health data and get intelligent recommendations.
- **Pet Profiles** – Store medical history, dietary needs, activity logs, and more.
- **Appointment Scheduling** – Manage vet visits and medication reminders.
- **Notification System** – Stay up to date on upcoming tasks or alerts.
- **Modern UI** – Clean, user-friendly interface with responsive design.

## 🛠️ Tech Stack

- **Backend**: Laravel 9.x
- **Frontend**: Tailwind CSS, Vite
- **Containerization**: Laravel Sail (Docker)
- **Database**: MySQL
- **Package Management**: Composer, NPM

## 📦 Getting Started

### Prerequisites

Before you begin, make sure you have the following installed:

- [Docker](https://www.docker.com/get-started)
- [Git](https://git-scm.com/downloads)

### Installation

1. **Clone the Repository**

```git clone https://github.com/Saraphoo/PurFurredPetPilot.git ```
```cd PurFurredPetPilot```


2. **Install Laravel Sail and Dependencies**

```composer install```
```php artisan sail:install```


3. **Copy Environment File**

```cp .env.example .env ```


4. **Generate Application Key**

```./vendor/bin/sail up -d```
```./vendor/bin/sail artisan key:generate```


5. **Run Migrations**

```./vendor/bin/sail artisan migrate```


6. **(Optional) Seed the Database**

```./vendor/bin/sail artisan db:seed```


7. **Access the App**

Open your browser and go to:

```http://localhost```

## 🧪 Running Tests

To run the automated test suite:

```./vendor/bin/sail test```

## 🐳 Common Laravel Sail Commands

- Start containers:  
```./vendor/bin/sail up -d```

- Stop containers:  
```./vendor/bin/sail down```

- Run Artisan commands:  
```./vendor/bin/sail artisan <command>```


- Run Composer commands:  
```./vendor/bin/sail composer <command>```


- Run NPM commands:  
```./vendor/bin/sail npm <command>```

## 📂 Project Structure

- `app/` – Laravel application core (Models, Controllers, etc.)
- `database/` – Migrations, factories, seeders
- `routes/` – Web and API route files
- `resources/` – Blade templates, JS, CSS
- `tests/` – Feature and unit test files

## 🤝 Contributing

Feel free to fork this repository and contribute via pull requests. Issues and suggestions are always welcome!

## 📄 License

This project is licensed under the [MIT License](LICENSE).
