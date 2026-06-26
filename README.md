# iBox Flight Aggregator API

A Laravel-based flight search aggregator that unifies results
from multiple providers into a single, deduplicated response, stores booking and finds booking info.

---

## Tech Stack

- PHP 8.2
- Laravel 11
- SQLite

---

## Architecture & Design Patterns

### Service Layer
Business logic is kept in dedicated Service classes,
keeping Controllers thin.

### Adapter Pattern
Each provider has its own Adapter that normalizes
provider-specific schemas into a unified FlightDTO.

### DTO (Data Transfer Object)
FlightDTO ensures type-safe data transfer between
the Adapter and Service layers.

---

## Project Structure
app/

├── Adapters/          # Provider-specific normalization

├── Contracts/         # ProviderAdapterInterface

├── DTOs/              # FlightDTO

├── Http/

│   ├── Controllers/   

│   ├── Requests/      

│   └── Resources/     

├── Models/            

├── Services/          # Business logic

└── Traits/            # ApiResponse


---

## Local Setup

### Requirements
- PHP 8.2+
- Composer

### Installation

```bash
git clone https://github.com/kaiser-hamid/ibox-flight-aggregator.git
cd ibox-flight-aggregator
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### Running the Application

This project requires **two terminal processes:**

```bash
# Terminal 1 — Main API (port 8000)
php artisan serve --port=8000

# Terminal 2 — Mock Provider Server (port 8001)
php artisan serve --port=8001
```

Mock providers simulate real third-party flight data
sources as local HTTP endpoints on a separate port
to avoid single-worker deadlock.

---

## API Reference

### Flight Search

GET /api/flights/search

| Parameter    | Type    | Required | Description              |
|--------------|---------|----------|--------------------------|
| `from`       | string  | Yes      | Origin IATA code         |
| `to`         | string  | Yes      | Destination IATA code    |
| `date`       | string  | Yes      | Format: `YYYY-MM-DD`     |
| `passengers` | integer | No       | Min: 1, Max: 9           |
| `sort_by`    | string  | No       | `price` (default) or `departure` |
| `stops`      | integer | No       | Filter by number of stops|
| `max_price`  | numeric | No       | Filter by maximum price  |

**Response:**
```json
{
  "data": [
    {
      "flight_id":      "EK585_DAC_DXB_2026-07-01",
      "flight_number":  "EK585",
      "carrier":        "EK",
      "from":           "DAC",
      "to":             "DXB",
      "departure_time": "2026-07-01T03:45:00+00:00",
      "arrival_time":   "2026-07-01T06:50:00+00:00",
      "duration_mins":  185,
      "stops":          0,
      "price":          399.00,
      "currency":       "USD",
      "source":         "provider_b"
    }
  ],
  "meta": {
    "total":               7,
    "providers_queried":   3,
    "providers_succeeded": 3,
    "providers_failed":    0, 
    "complete":            true,
    "filters_applied": {
      "from":       "DAC",
      "to":         "DXB",
      "date":       "2026-07-01",
      "passengers": 2
    }
  }
}
```

---

### Create Booking

POST /api/bookings

**Request Body:**
```json
{
    "flight_id": "BS118_DAC_DXB_2026-07-01",
    "flight_data": {
        "flight_id": "BS118_DAC_DXB_2026-07-01",
        "carrier": "BS",
        "from": "DAC",
        "to": "DXB",
        "price": "265",
        "currency": "USD",
        "source": "provider_a"
    },
    "passengers": [
        {
            "name": "Kaiser Hamid",
            "email": "kaiser@example.com",
            "passport": "A13276623"
        }
    ]
}
```

**Response:** `201 Created`
```json
{
    "success": true,
    "message": "Booking created successfully.",
    "data": {
        "reference": "BK-6RZMXJ",
        "flight_id": "BS118_DAC_DXB_2026-07-01",
        "flight_data": {
            "flight_id": "BS118_DAC_DXB_2026-07-01",
            "carrier": "BS",
            "from": "DAC",
            "to": "DXB",
            "price": "265",
            "currency": "USD",
            "source": "provider_a"
        },
        "passengers": [
            {
                "name": "Kaiser Hamid",
                "email": "kaiser@example.com",
                "passport": "A13276623"
            }
        ],
        "total_price": 265,
        "currency": "USD",
        "created_at": "2026-06-26T13:15:41+00:00"
    }
}
```

---

### Get Booking

GET /api/bookings/{reference}

**Response:** `200 OK`
```json
{
    "success": true,
    "message": "Booking retrieved successfully.",
    "data": {
        "reference": "BK-5ARROM",
        "flight_id": "BS118_DAC_DXB_2026-07-01",
        "flight_data": {
            "flight_id": "BS118_DAC_DXB_2026-07-01",
            "carrier": "BS",
            "from": "DAC",
            "to": "DXB",
            "price": "265",
            "currency": "USD",
            "source": "provider_a"
        },
        "passengers": [
            {
                "name": "Kaiser Hamid",
                "email": "kaiser@example.com",
                "passport": "A13276623"
            }
        ],
        "total_price": 265,
        "currency": "USD",
        "created_at": "2026-06-26T13:11:06+00:00"
    }
}
```

---

## Key Decisions
**Stable flight identifier:**
The stable flight identifier is `flight_id` that is made with the combination of `{flight_number}_{from}_{to}_{departure_date}`.


**Deduplication:**
Same flight appearing across multiple providers is
deduplicated by `flight_id`.
The lowest price is retained.

**Flight Snapshot on Booking:**
Flight data is stored as a JSON snapshot at booking time,
ensuring booking records remain self-contained even if
provider data changes.

**Mock Providers as HTTP Endpoints:**
Providers are simulated as local HTTP endpoints on a
separate port to mirror real integration patterns.

**Source Tracking:**
Each flight includes a `source` field indicating which
provider returned the cheapest result, useful for
traceability and debugging.

---

## Assumptions & Limitations

- All prices are assumed to be in USD
- No authentication or authorization implemented
- Provider responses are not cached
- No pagination on flight results

---

## Future Improvements

- Unit and feature test coverage
- Redis caching for provider responses
- Pagination for large result sets
- Currency conversion support
- Provider timeout and retry handling
