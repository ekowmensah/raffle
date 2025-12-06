# Raffle System API Documentation

## Base URL
```
https://your-domain.com/api
```

## Authentication
Most endpoints require authentication using JWT tokens in the Authorization header:
```
Authorization: Bearer {your-jwt-token}
```

---

## Authentication Endpoints

### Send OTP
Send OTP code for phone verification.

**Endpoint:** `POST /api/auth/send-otp`

**Request Body:**
```json
{
  "phone": "0241234567"
}
```

**Response:**
```json
{
  "success": true,
  "message": "OTP sent successfully",
  "data": []
}
```

---

### Verify OTP & Login
Verify OTP and login/register user.

**Endpoint:** `POST /api/auth/verify-otp`

**Request Body:**
```json
{
  "phone": "0241234567",
  "otp": "123456",
  "name": "John Doe",
  "email": "john@example.com"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "player": {
      "id": 1,
      "name": "John Doe",
      "phone": "233241234567",
      "email": "john@example.com",
      "loyalty_points": 0
    }
  }
}
```

---

### Get Current User
Get authenticated user's profile.

**Endpoint:** `GET /api/auth/me`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": {
    "id": 1,
    "name": "John Doe",
    "phone": "233241234567",
    "email": "john@example.com",
    "loyalty_points": 150,
    "created_at": "2024-01-01 10:00:00"
  }
}
```

---

### Update Profile
Update user profile.

**Endpoint:** `PUT /api/auth/profile`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "name": "John Smith",
  "email": "johnsmith@example.com"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Profile updated successfully",
  "data": {
    "id": 1,
    "name": "John Smith",
    "phone": "233241234567",
    "email": "johnsmith@example.com",
    "loyalty_points": 150
  }
}
```

---

## Campaign Endpoints

### Get All Campaigns
Get list of active campaigns.

**Endpoint:** `GET /api/campaigns`

**Query Parameters:**
- `station_id` (optional): Filter by station
- `programme_id` (optional): Filter by programme

**Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": [
    {
      "id": 1,
      "name": "Christmas Raffle 2024",
      "description": "Win amazing prizes this Christmas!",
      "ticket_price": 5.00,
      "start_date": "2024-12-01",
      "end_date": "2024-12-25",
      "final_draw_date": "2024-12-26",
      "is_active": true,
      "sponsor": {
        "id": 1,
        "name": "ABC Company"
      }
    }
  ]
}
```

---

### Get Campaign Details
Get detailed information about a campaign.

**Endpoint:** `GET /api/campaigns/{id}`

**Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": {
    "id": 1,
    "name": "Christmas Raffle 2024",
    "description": "Win amazing prizes this Christmas!",
    "ticket_price": 5.00,
    "start_date": "2024-12-01",
    "end_date": "2024-12-25",
    "final_draw_date": "2024-12-26",
    "is_active": true,
    "total_tickets_sold": 5000,
    "current_prize_pool": 15000.00,
    "sponsor": {
      "id": 1,
      "name": "ABC Company",
      "logo": "logo.png"
    },
    "revenue_sharing": {
      "platform_percent": 10.00,
      "station_percent": 20.00,
      "programme_percent": 10.00,
      "prize_pool_percent": 60.00
    },
    "upcoming_draws": [
      {
        "id": 1,
        "draw_date": "2024-12-10",
        "draw_type": "daily",
        "status": "pending"
      }
    ]
  }
}
```

---

### Get Stations
Get list of active stations.

**Endpoint:** `GET /api/stations`

**Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": [
    {
      "id": 1,
      "name": "Joy FM",
      "code": "JOY",
      "description": "Your favorite station"
    }
  ]
}
```

---

### Get Programmes by Station
Get programmes for a specific station.

**Endpoint:** `GET /api/stations/{stationId}/programmes`

**Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": [
    {
      "id": 1,
      "name": "Morning Show",
      "station_id": 1,
      "description": "Start your day right"
    }
  ]
}
```

---

## Ticket Endpoints

### Get My Tickets
Get authenticated user's tickets.

**Endpoint:** `GET /api/tickets`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `campaign_id` (optional): Filter by campaign
- `page` (optional): Page number
- `per_page` (optional): Items per page

**Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": [
    {
      "id": 1,
      "ticket_code": "TKT-ABC123",
      "campaign_id": 1,
      "campaign_name": "Christmas Raffle 2024",
      "purchase_date": "2024-12-01 10:00:00",
      "status": "active"
    }
  ]
}
```

---

### Get Ticket Details
Get details of a specific ticket.

**Endpoint:** `GET /api/tickets/{code}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": {
    "id": 1,
    "ticket_code": "TKT-ABC123",
    "campaign": {
      "id": 1,
      "name": "Christmas Raffle 2024"
    },
    "station": {
      "id": 1,
      "name": "Joy FM"
    },
    "programme": {
      "id": 1,
      "name": "Morning Show"
    },
    "purchase_date": "2024-12-01 10:00:00",
    "status": "active",
    "is_winner": false,
    "prize": null
  }
}
```

---

### Purchase Tickets
Initiate ticket purchase.

**Endpoint:** `POST /api/tickets/purchase`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "campaign_id": 1,
  "station_id": 1,
  "programme_id": 1,
  "quantity": 2,
  "payment_method": "mtn_momo"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Payment created successfully",
  "data": {
    "payment_id": 123,
    "reference": "APP1234567890",
    "amount": 10.00,
    "quantity": 2,
    "status": "pending",
    "message": "Payment initiated. Complete payment to receive tickets."
  }
}
```

---

### Verify Ticket
Verify a ticket code.

**Endpoint:** `POST /api/tickets/verify`

**Request Body:**
```json
{
  "ticket_code": "TKT-ABC123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Ticket verified",
  "data": {
    "id": 1,
    "ticket_code": "TKT-ABC123",
    "campaign": {
      "id": 1,
      "name": "Christmas Raffle 2024"
    },
    "status": "active",
    "is_winner": false
  }
}
```

---

### Get Ticket Statistics
Get user's ticket statistics.

**Endpoint:** `GET /api/tickets/stats`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": {
    "total_tickets": 25,
    "active_tickets": 15,
    "total_spent": 125.00,
    "total_wins": 2,
    "total_winnings": 500.00,
    "loyalty_points": 150
  }
}
```

---

## Draw Endpoints

### Get Draws
Get list of draws.

**Endpoint:** `GET /api/draws`

**Query Parameters:**
- `campaign_id` (optional): Filter by campaign
- `draw_type` (optional): Filter by type (daily, final, bonus)
- `status` (optional): Filter by status (pending, completed)

**Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": [
    {
      "id": 1,
      "campaign_id": 1,
      "campaign_name": "Christmas Raffle 2024",
      "draw_date": "2024-12-10",
      "draw_type": "daily",
      "status": "completed",
      "winner_count": 5
    }
  ]
}
```

---

### Get Draw Details
Get details of a specific draw.

**Endpoint:** `GET /api/draws/{id}`

**Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": {
    "id": 1,
    "campaign_id": 1,
    "campaign_name": "Christmas Raffle 2024",
    "draw_date": "2024-12-10",
    "draw_type": "daily",
    "status": "completed",
    "winner_count": 5,
    "prize_pool": 5000.00,
    "winners": [
      {
        "id": 1,
        "prize_rank": 1,
        "prize_amount": 2000.00,
        "ticket_code": "TKT-XYZ789",
        "player_name": "J***n",
        "paid_status": "paid"
      }
    ]
  }
}
```

---

### Get Upcoming Draws
Get upcoming scheduled draws.

**Endpoint:** `GET /api/draws/upcoming`

**Query Parameters:**
- `campaign_id` (optional): Filter by campaign

**Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": [
    {
      "id": 2,
      "campaign_id": 1,
      "campaign_name": "Christmas Raffle 2024",
      "draw_date": "2024-12-15",
      "draw_type": "daily",
      "status": "pending",
      "winner_count": 5
    }
  ]
}
```

---

### Get Recent Winners
Get recent draw winners.

**Endpoint:** `GET /api/draws/recent-winners`

**Query Parameters:**
- `limit` (optional): Number of results (default: 20)
- `campaign_id` (optional): Filter by campaign

**Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": [
    {
      "id": 1,
      "campaign_name": "Christmas Raffle 2024",
      "draw_date": "2024-12-10",
      "draw_type": "daily",
      "prize_rank": 1,
      "prize_amount": 2000.00,
      "ticket_code": "TKT-XYZ789",
      "player_name": "J***n",
      "player_phone": "024***4567"
    }
  ]
}
```

---

### Get My Wins
Get authenticated user's wins.

**Endpoint:** `GET /api/draws/my-wins`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": [
    {
      "id": 1,
      "campaign_name": "Christmas Raffle 2024",
      "draw_date": "2024-12-10",
      "draw_type": "daily",
      "prize_rank": 1,
      "prize_amount": 2000.00,
      "ticket_code": "TKT-ABC123",
      "paid_status": "paid",
      "paid_at": "2024-12-11 10:00:00"
    }
  ]
}
```

---

## Error Responses

All error responses follow this format:

```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field_name": "Error description"
  }
}
```

### HTTP Status Codes
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

---

## Rate Limiting
API requests are limited to:
- 100 requests per minute for authenticated users
- 20 requests per minute for unauthenticated users

---

## Support
For API support, contact: support@raffle-system.com
