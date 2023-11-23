## Installing and Running the News App

**Using following News service as data source**

- https://newsapi.org/
- https://open-platform.theguardian.com/
- https://developer.nytimes.com/docs/articlesearch-product/1/overview

**Prerequisites:**

- PHP 8.2
- Composer 2
- Docker

### Installation

1. **Install dependencies:**

```bash
composer install
```

2. **Copy the environment configuration file:**

```bash
cp .env.example .env
```

3. **Set the API keys:**
```bash
NEWS_API_KEY=your_news_api_key
GUARDIAN_API_KEY=your_guardian_api_key
NEW_YORK_TIMES_API_KEY=your_new_york_times_api_key
```

4. **Build the Docker image:**

```bash
./vendor/bin/sail build
```

5. **Start the project:**

```bash
./vendor/bin/sail up -d
```

6. **Set the application key:**

```bash
./vendor/bin/sail artisan key:generate
```

8. **Run database migrations:**

```bash
./vendor/bin/sail artisan migrate
```

9. **Dispatch News Import Job:**

```bash
./vendor/bin/sail artisan news:import
```

### Accessing the Application

Once the project is up and running, you can access it at [https://localhost](https://localhost)
