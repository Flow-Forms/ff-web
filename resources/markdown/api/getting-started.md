---
title: API Getting Started
order: 1
---

# API Getting Started

The Flow Forms API allows you to programmatically access your forms and submissions. Build custom integrations, automate workflows, and extend Flow Forms functionality.

## Authentication

### API Tokens
All API requests require authentication using API tokens:

1. **Generate a Token**
   - Navigate to Admin → Account (admin only)
   - Click "API Tokens"
   - Enter a token name
   - Click "Generate New Token"
   - **Important**: Copy your token immediately - it won't be shown again

2. **Using Your Token**
   Include your token in the Authorization header:
   ```
   Authorization: Bearer YOUR_API_TOKEN
   ```

### Token Security
- Keep tokens secret and secure
- Use environment variables
- Rotate tokens regularly
- One token per integration

## Base URL

All API requests use this base URL:
```
https://api.flowforms.io/api/v1
```

## Making Your First Request

### List Submissions
```bash
curl -X POST https://api.flowforms.io/api/v1/submission \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "filters": {
      "form_id": 123
    }
  }'
```

### Get Single Submission
```bash
curl https://api.flowforms.io/api/v1/submission/456 \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

## Available Endpoints

### Submissions
- `POST /api/v1/submission` - List submissions with filters
- `GET /api/v1/submission/{id}` - Get single submission

### Response Format

All responses return JSON:

```json
{
  "data": {
    "id": "456",
    "form_id": "123",
    "status": "completed",
    "submitted_at": "2024-01-15T10:30:00Z",
    "fields": {
      "name": "John Doe",
      "email": "john@example.com"
    }
  },
  "success": true
}
```

## Filtering Submissions

### Filter Options
```json
{
  "filters": {
    "form_id": 123,
    "status": "pending",
    "submitted_by": "user@example.com",
    "date_from": "2024-01-01",
    "date_to": "2024-01-31"
  },
  "page": 1,
  "per_page": 50
}
```

### Pagination
Responses include pagination data:
```json
{
  "data": [...],
  "pagination": {
    "current_page": 1,
    "per_page": 50,
    "total": 150,
    "last_page": 3
  }
}
```

## Error Handling

### Error Response Format
```json
{
  "error": {
    "code": "INVALID_TOKEN",
    "message": "The provided API token is invalid"
  },
  "success": false
}
```

### Common Error Codes
- `INVALID_TOKEN` - Authentication failed
- `NOT_FOUND` - Resource doesn't exist
- `PERMISSION_DENIED` - Insufficient permissions
- `RATE_LIMITED` - Too many requests
- `VALIDATION_ERROR` - Invalid request data

## Rate Limiting

API requests are rate limited to ensure fair usage:
- 1000 requests per hour
- Rate limit info in response headers
- `X-RateLimit-Remaining`
- `X-RateLimit-Reset`

## Best Practices

### Development
- Use a separate token for testing
- Log API responses for debugging
- Handle errors gracefully
- Implement exponential backoff

### Production
- Cache responses when appropriate
- Minimize API calls
- Use webhooks for real-time updates
- Monitor API usage

## SDK Support

While Flow Forms doesn't provide official SDKs, the API works with any HTTP client:
- JavaScript: Axios, Fetch
- Python: Requests
- PHP: Guzzle
- Ruby: HTTParty

## Getting Help

- Check response error messages
- Review API logs in your account
- Contact support for assistance