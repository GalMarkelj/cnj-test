# Laravel Inertia React Housing Project

This is a Laravel Inertia React project wrapped in Docker and managed using Laravel Sail. The project currently includes
only a housing document upload and statistics feature.

## 📖 Feature Documentation

For detailed documentation on the housing feature, please refer to the [docs/housing](docs/housing) directory.

---

## 🚀 Requirements

Ensure you have the following installed on your system:

- **Docker** (with Docker Compose)
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

### 3️⃣ Start Docker Containers

```sh
sail up
```

### 4️⃣ Run the following commands

```sh
sail artisan key:generate
sail artisan migrate
```

### 5️⃣ Install Dependencies

For the frontend dependencies:

```sh
sail npm install
```

### 6️⃣ Access the Application

Once the setup is complete, open your browser and go to:

```
http://localhost
```

---

## 🧪 Running Tests

### Create testing environment configuration and only set `DB_DATABASE=testing`.

```sh
cp .env .env.testing
cp .env .env.cypress
```

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

You can do this with the interface:

```sh
npx cypress open
```

Or in headless more (in terminal):

```sh
npx cypress run
```

---
