# TASK:

Practical task for deep tech:
Develop a Laravel-Based Backend for Frontend Integration with a set of RESTful
endpoints to meet the following criteria:
- Create a Job - POST /api/jobs - Accept a JSON request body that includes array of
URLs to scrape and HTML/CSS selectors.
- Retrieve Job by ID - GET /api/jobs/{id} - Return job details and scraped data from
URL.
- Delete Job by ID - DELETE /api/jobs/{id} - Remove job.
## Redis Data Store:
• Utilize Redis as the data store to maintain job details, statuses, and scraped data.
## Background Processing (Optional):
• Implement background processing (e.g., Laravel queues) to perform web scraping tasks
asynchronously.
## Docker Containers (Bonus Points):
• Optionally, set up Docker containers for the Laravel application, including necessary
services like PHP, Nginx/Apache, and Redis, earning bonus points.


# INSTALL:

- Clone source
- Install redis
- Install composer
> composer install
- Run serve
> php artisan serve

# API DOCUMENT:

{host}/api/documentation#/