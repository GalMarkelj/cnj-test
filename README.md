# Laravel Inertia React Housing Project

This is a Laravel Inertia React project wrapped in Docker and managed using Laravel Sail. The project currently includes
only a housing document upload and statistics feature.

## 📖 Feature Documentation

For detailed documentation on the housing feature, please refer to the [docs/housing](docs/housing) directory.

---

## 🚀 Requirements

Ensure you have the following installed on your system:

- **Docker** (with Docker Compose)
- **Composer**
- **Laravel Sail** (included in the project)
- **Node.js** and **npm** (for frontend development)

### Recomendation

- Create a 'sail' alias in your `.bashrc` or `.zshrc`, or whatever shell you are using:

```shell
alias sail='[ -f sail ] && bash ./sail || bash ./vendor/bin/sail'
```

And then refresh the session with `source ~/.bashrc`.

---

## 🛠️ Setup Instructions

### 1️⃣ Clone the Repository

```sh
git clone <repository-url>
cd <project-directory>
```

### 2️⃣ Create environment configuration and ask the owner for the information.

```sh
cp .env.example .env
```

### 3️⃣ Install Backend Dependencies

```sh
composer install
```

### 4️⃣ Start Docker Containers

```sh
sail up
```

### 5️⃣ Run key generation and migration

```sh
sail artisan key:generate
sail artisan migrate
```

### 6️⃣ Setup testing environment

#### ⚠️ Important

Tests are running on a separate testing database. After you copy your .env to .env.testing, set the following fields:

```.dotenv
APP_ENV=testing
DB_DATABASE=testing
```

Everything else should be the same as in the .env, except the APP_KEY that is generated bellow.

```sh
cp .env.example .env.testing

sail artisan key:generate --env=testing
sail artisan migrate --env=testing

cp .env.testing .env.cypress
```

### 7️⃣ Install Frontend Dependencies

#### ⚠️ Important - delete cypress from the package.json first and install it without sail!

I'll add a command in the next step.

```sh
sail npm install
```

### 8️⃣ Run Frontend

For the frontend dependencies:

```sh
sail npm run dev
```

### 9️⃣ Access the Application

Once the setup is complete, open your browser and go to:

```
http://localhost
```

---

## 🧪 Running Tests

The project includes **PEST** for backend testing and **Cypress** for end-to-end testing.

### ⚠️ To run both PEST and Cypress tests, both, backend and frontend must be running:

```sh
sail up
sail npm run dev
```

### Run PEST Tests

Because there is a separate database for testing, you need to run the tests inside the container.

```sh
sail test
```

### Run Cypress Tests

#### ⚠️ Cypress needs additional packages to run properyly.

You have to go on to the container, and install the required dependencies.

1. Get id of the container. Look for something like `laravel.test`.

```sh
docker ps
```

2. Enter in to the container

```sh
docker exec -it [CONTAINER_ID] bash
```

3. Update the system there and install the required deps

```sh
apt-get update

apt-get install -y \
  libgtk-3-0 \
  libgbm1 \
  libnotify4 \
  libgdk-pixbuf2.0-0 \
  libcups2 \
  libxss1 \
  libnss3 \
  xvfb \
  libpangocairo-1.0-0 \
  libpangoft2-1.0-0
  
 exit
```

Run the tests

```sh
sail npx cypress run
```

---
