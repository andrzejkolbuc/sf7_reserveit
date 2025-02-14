# ReserveIt Backend API Documentation

This document describes the REST API endpoints for the ReserveIt room reservation system.

## Base URL

```
http://localhost:8000
```

## Room Management API

### List Rooms
```http
GET /rooms
```

**Response**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Conference Room A",
      "capacity": 10,
      "description": "Main conference room"
    }
  ]
}
```

### Create Room
```http
POST /rooms
```

**Request Body**
```json
{
  "name": "Conference Room A",
  "capacity": 10,
  "description": "Main conference room"
}
```

**Required Fields**
- `name`: String (2-255 characters)
- `capacity`: Integer (positive number)

**Optional Fields**
- `description`: String (nullable)

**Response (201 Created)**
```json
{
  "data": {
    "id": 1,
    "name": "Conference Room A",
    "capacity": 10,
    "description": "Main conference room"
  }
}
```

### Get Room
```http
GET /rooms/{id}
```

**Response**
```json
{
  "data": {
    "id": 1,
    "name": "Conference Room A",
    "capacity": 10,
    "description": "Main conference room"
  }
}
```

### Update Room
```http
PUT /rooms/{id}
```

**Request Body**
```json
{
  "name": "Updated Room Name",
  "capacity": 15,
  "description": "Updated description"
}
```

All fields are optional in the update request. Only provided fields will be updated.

### Delete Room
```http
DELETE /rooms/{id}
```

**Response**: 204 No Content

Note: Cannot delete rooms that have existing reservations (returns 409 Conflict).

## Reservation Management API

### List Reservations
```http
GET /reservations
```

**Response**
```json
{
  "data": [
    {
      "id": 1,
      "room": {
        "id": 1,
        "name": "Conference Room A"
      },
      "startTime": "2025-02-14T14:00:00+00:00",
      "endTime": "2025-02-14T16:00:00+00:00",
      "title": "Team Meeting",
      "description": "Weekly sync"
    }
  ]
}
```

### Create Reservation
```http
POST /reservations
```

**Request Body**
```json
{
  "roomId": 1,
  "startTime": "2025-02-14T14:00:00+01:00",
  "endTime": "2025-02-14T16:00:00+01:00",
  "title": "Team Meeting",
  "description": "Weekly sync"
}
```

**Required Fields**
- `roomId`: Integer (must reference existing room)
- `startTime`: String (ISO 8601 datetime)
- `endTime`: String (ISO 8601 datetime)
- `title`: String (2-255 characters)

**Optional Fields**
- `description`: String (nullable)

**Validation Rules**
- End time must be after start time
- Time slot must not overlap with existing reservations for the same room

**Response (201 Created)**
```json
{
  "data": {
    "id": 1,
    "room": {
      "id": 1,
      "name": "Conference Room A"
    },
    "startTime": "2025-02-14T14:00:00+01:00",
    "endTime": "2025-02-14T16:00:00+01:00",
    "title": "Team Meeting",
    "description": "Weekly sync"
  }
}
```

### Get Reservation
```http
GET /reservations/{id}
```

**Response**
```json
{
  "data": {
    "id": 1,
    "room": {
      "id": 1,
      "name": "Conference Room A"
    },
    "startTime": "2025-02-14T14:00:00+00:00",
    "endTime": "2025-02-14T16:00:00+00:00",
    "title": "Team Meeting",
    "description": "Weekly sync"
  }
}
```

### Update Reservation
```http
PUT /reservations/{id}
```

**Request Body**
```json
{
  "roomId": 1,
  "startTime": "2025-02-14T14:00:00+01:00",
  "endTime": "2025-02-14T16:00:00+01:00",
  "title": "Updated Meeting",
  "description": "Updated description"
}
```

All fields are optional in the update request. Only provided fields will be updated.
The same validation rules apply as in creation.

### Delete Reservation
```http
DELETE /reservations/{id}
```

**Response**: 204 No Content

## Error Responses

The API uses standard HTTP status codes and returns error messages in a consistent format:

```json
{
  "error": "Error message description"
}
```

### Status Codes
- `200`: Success
- `201`: Created
- `204`: No Content (successful deletion)
- `400`: Bad Request (validation errors)
- `404`: Not Found
- `409`: Conflict (overlapping reservations or cannot delete room with reservations)

### Common Validation Errors
- Room name must be between 2 and 255 characters
- Room capacity must be a positive number
- Reservation title must be between 2 and 255 characters
- Reservation end time must be after start time
- Cannot create overlapping reservations for the same room
- Cannot delete a room that has existing reservations
