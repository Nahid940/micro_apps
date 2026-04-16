This project is a microservices architecture experiment built with multiple Laravel applications, Docker, and an Nginx API Gateway.

It demonstrates service-to-service communication, centralized routing, and containerized development.

<p>
🧱 Architecture
Client
  │
  ▼
API Gateway (Nginx)
  │
  ├── User Service (Laravel)
  ├── Order Service (Laravel)
  └── (future services...)
</p>

- 🚀 Services
- 👤 User Service Handles user-related APIs Runs independently
- Exposed internally via Docker network
- 📦 Order Service
 - Handles order-related APIs
 - Communicates with User Service when needed
- 🌐 API Gateway (Nginx)
 - Single entry point for all requests
 - Routes traffic to appropriate services
 - Handles request forwarding
- 🐳 Tech Stack
- Laravel (multiple services)
 - Docker & Docker Compose
 - Nginx (API Gateway)
 - MySQL (per service database)