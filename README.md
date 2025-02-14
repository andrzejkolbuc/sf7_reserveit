# ReserveIt

ReserveIt is a web application that allows users to manage corporate resource reservations such as conference rooms. The system consists of a Symfony-based API and a Next.js frontend, providing a seamless experience for managing rooms and their reservations based on calendar view.
Conference rooms are stored in the database, and notifications about new reservations are sent using RabbitMQ. 

All custom code are provided in Symfony bundle and Next.js app.

Symfony is used as REST API to manage resources, while Next.js is used to provide a user-friendly interface based on calendar view for creating and managing reservations.
All operations (create, read, update, delete) are handled through the API, and the validation of the data is done on the backend.
Frontend is built using Next.js, providing a modern and responsive user interface based on calendar view to manage reservations.

## Features

### Backend 

- REST API for managing conference rooms.
- Ability to add, edit, and delete conference rooms.
- Reservation management (date, start and end time, and the name of the reserving person).
- Reservation validation (preventing overlapping bookings).
- Based on Symfony and PostgreSQL.
- RabbitMQ for handling notifications about new reservations
- Redis to handle concurrent bookings.

### Frontend 

- Admin panel with a list of conference rooms.
- Form for adding, deleting and editing rooms.
- Reservation calendar with the ability to create new reservations with visualisation of reserved time slots.

## Deployment Environment (Docker)

The application is delivered as Docker containers, allowing easy installation and execution. The configuration includes:

- Backend (PHP 8.3, Symfony 7 + PostgreSQL + RabbitMQ + Redis).
- Frontend (Next.js, Tailwind CSS).

## Technologies

### Backend
- PHP 8.3
- Symfony 7
- PostgreSQL
- RabbitMQ
- Redis
- Doctrine ORM 
- PHPUnit

### Frontend
- Next.js 15
- React 18
- TypeScript
- TailwindCSS 

### Development Tools
- Docker and Docker Compose
- ESLint 
- Prettier 
- TypeScript ESLint

## System Requirements

- Docker and Docker Compose

## Installation and Setup

1. Clone the repository:
   ```sh
   git clone https://github.com/andrzejkolbuc/reserveit.git
   cd reserveit
   ```
2. Start the application using Docker Compose:
   ```sh
   docker-compose up -d
   ```
3. The application should be accessible at:
   - Backend: `http://localhost:8000`
   - Frontend: `http://localhost:3000`

## Authors

Andrzej Kołbuc (kolbucandrzej@gmail.com)

https://github.com/andrzejkolbuc/reserveit.git

All rights reserved.
