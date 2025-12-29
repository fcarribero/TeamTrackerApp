# TeamTrackerApp

TeamTrackerApp is a comprehensive management system for sports teams or fitness classes, built with Laravel. It provides dedicated dashboards for both professors and students, allowing for efficient tracking of workouts, payments, and student progress.

## Features

### For Professors
- **Student Management:** Create, edit, and track students.
- **Group Management:** Organize students into groups.
- **Workout Planning:** Create workout templates and assign specific training sessions to students or groups.
- **Payment Tracking:** Manage student payments and subscription statuses.
- **Announcements:** Send notifications and announcements to students.
- **Statistics Dashboard:** View overall performance and participation stats.

### For Students
- **Personal Dashboard:** Access assigned workouts and track progress.
- **Workout Completion:** Mark workouts as completed and record results.
- **Payment Overview:** Keep track of personal payment history.
- **Announcements:** Receive updates from instructors.

## Technologies Used
- **Backend:** Laravel 12.x, PHP 8.3
- **Frontend:** Blade Templates, Tailwind CSS, Vite
- **Database:** MySQL / SQLite
- **AI Integration:** OpenAI API (GPT-4o-mini) for enhanced features
- **Containerization:** Docker & Docker Compose

## Requirements
- PHP >= 8.2
- Composer
- Node.js & NPM
- Docker (optional, for containerized setup)

## Installation

### Using Docker (Recommended)
1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd TeamTrackerApp
   ```
2. Start the containers:
   ```bash
   docker-compose up -d
   ```
3. Enter the app container:
   ```bash
   docker-compose exec app bash
   ```
4. Run the setup command:
   ```bash
   composer run setup
   ```

### Manual Setup
1. Clone the repository and navigate to the `src` directory:
   ```bash
   git clone <repository-url>
   cd TeamTrackerApp/src
   ```
2. Install PHP dependencies:
   ```bash
   composer install
   ```
3. Set up the environment file:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Configure your database in `.env`.
5. Run migrations and seeders:
   ```bash
   php artisan migrate
   ```
6. Install and build frontend assets:
   ```bash
   npm install
   npm run build
   ```
7. Start the development server:
   ```bash
   php artisan serve
   ```

## OpenAI Integration
To use the AI features, add your OpenAI API key to the `.env` file:
```env
OPENAI_API_KEY=your_api_key_here
OPENAI_MODEL=gpt-4o-mini
```

## License
This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
