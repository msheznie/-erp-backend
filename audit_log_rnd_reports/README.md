# ERP Audit Log System - Main Documentation

## Overview

This directory contains comprehensive documentation about the ERP system's audit logging architecture, including the current implementation and research findings for improvements.

---

## Documentation Files

### 1. Current System Documentation
📄 **[AUDIT_LOG_DOCUMENTATION.md](./AUDIT_LOG_DOCUMENTATION.md)**

This document describes the **current logging workflow** used in the ERP system. It covers:

- **4-layer logging architecture**: Laravel → Fluent Bit → Loki → Grafana
- **How logs are stored**: Complete flow from user action to Loki storage
- **How logs are queried**: API endpoints, LogQL queries, and frontend integration
- **Supported modules/tables**: All tracked audit log entities
- **Docker setup**: Fluent Bit configuration and deployment
- **Troubleshooting guide**: Common issues and solutions

**Key Architecture:**
```
User Action → Controller → AuditLogsTrait → AuditLogJob → audit.log file → Fluent Bit → Loki → Grafana/LokiService
```

**Use this document when:**
- Understanding how the current system works
- Troubleshooting existing log issues
- Onboarding new team members
- Maintaining the current Loki-based system

---

### 2. R&D Findings Documentation
📄 **[NEW_AUDIT_LOG_ARCHITECTURE_RND.md](./NEW_AUDIT_LOG_ARCHITECTURE_RND.md)**

This document contains the **research and development findings** for improving the audit log system. It includes:

- **Problem statement**: Current system limitations and issues
- **Solution evaluation**: Comparison of VictoriaLogs, ClickHouse, Elasticsearch, and PostgreSQL
- **Performance analysis**: Detailed benchmarks and comparisons
- **Ingestion architecture options**: Direct HTTP, FluentBit, and Message Queue approaches
- **Recommendations**: Primary recommendation for VictoriaLogs with Direct HTTP ingestion
- **Migration strategy**: Phased approach for implementation

**Key Findings:**
- ✅ **VictoriaLogs** recommended as primary solution
- ✅ **94% faster queries** and **3x faster ingestion** than Loki
- ✅ **72% less CPU** and **87% less RAM** usage
- ✅ **40% less storage** requirements
- ✅ **Excellent high-cardinality support** (solves current Loki limitations)

**Use this document when:**
- Planning system improvements
- Understanding why VictoriaLogs was chosen
- Evaluating alternative solutions
- Planning migration strategy

---

## Environment Configuration for Victoria Logs

To use Victoria Logs in the ERP system, configure the following environment variables in your `.env` file:

### Required Environment Variables

```env
# Victoria Logs Configuration
VICTORIA_LOG_URL=https://php-auditlogs-rnd.gears-int.com/insert/loki/api/v1/push
VICTORIA_LOG_QUERY_URL=https://php-auditlogs-rnd.gears-int.com/select/logsql/query
VICTORIA_LOG_USERNAME=*****
VICTORIA_LOG_PASSWORD=*****

# Environment label for filtering logs
VICTORIALOGS_ENV=production

# Legacy Loki configuration (for backward compatibility)
LOKI_URL=http://loki-cp-dev.gears-int.com/loki/api/v1/
LOKI_ENV=erp-qa
LOKI_START_DATE=2024-01-01
```

## Test Data Generation Command

### Command Overview

The `GenerateTestAuditLogs` command is used to generate test audit logs for performance testing and system validation.

**Location:** `app/Console/Commands/GenerateTestAuditLogs.php`

**Command Signature:**
```bash
php artisan audit:generate-test-logs [options]
```

### Command Options

| Option | Description | Default | Example |
|--------|-------------|---------|---------|
| `--count` | Number of audit logs to generate | `1000` | `--count=10` |
| `--transaction-id` | Transaction ID (document primary ID) to use | `3449` | `--transaction-id=12345` |
| `--table` | Table name to use | `itemmaster` | `--table=customermaster` |

### Usage Examples

#### Basic Usage (Generate 10 logs)
```bash
php artisan audit:generate-test-logs --count=10
```

This generates 10 audit logs with default values:
- Transaction ID: `3449`
- Table: `itemmaster`

#### Custom Transaction ID and Table
```bash
php artisan audit:generate-test-logs --count=50 --transaction-id=12345 --table=customermaster
```

This generates 50 audit logs for:
- Transaction ID: `12345`
- Table: `customermaster`

#### Generate Large Volume for Performance Testing
```bash
php artisan audit:generate-test-logs --count=10000 --transaction-id=9999 --table=itemmaster
```

This generates 10,000 audit logs for performance testing.

### Running in Docker Container

If your application runs in a Docker container, use:

```bash
docker exec erp-backend php artisan audit:generate-test-logs --count=10
```

### Command Behavior

1. **Job Dispatch**: The command dispatches `SendToVictoriaLogJob` jobs to the queue for each generated log.

2. **Data Variety**: Each log entry includes randomized data:
   - Random CRUD types (Create, Update, Delete)
   - Random user names, employee IDs, roles
   - Random session IDs, document codes
   - Random date/time within the last 30 days
   - Random data changes (for Update operations)

3. **Locale**: All generated logs use **English (`en`)** locale only.

4. **Queue Processing**: Logs are sent to Victoria Logs as queue jobs are processed. Make sure your queue worker is running:

```bash
php artisan queue:work database --tries=3
```

### Generated Log Structure

Each generated log entry contains:

```json
{
    "channel": "audit",
    "transaction_id": "3449",
    "table": "itemmaster",
    "user_name": "John Doe",
    "role": "Manager",
    "employeeId": "1001",
    "tenant_uuid": "local",
    "crudType": "U",
    "narration": "INV002530 has been updated",
    "session_id": "SID120",
    "date_time": "2025-12-15 10:30:00",
    "module": "finance",
    "parent_id": null,
    "parent_table": null,
    "data": "[{\"amended_field\":\"unit_of_measure\",\"previous_value\":\"Each\",\"new_value\":\"Ltr\"}]",
    "locale": "en",
    "company_system_id": "1",
    "doc_code": "INV002530",
    "log_uuid": "a1b2c3d4e5f6..."
}
```

---

## Querying and Viewing Test Data

### Method 1: Using ERP Frontend Log Modal (Recommended)

The easiest way to view audit logs is through the ERP frontend's built-in audit log modal, which is available in most module detail views.

#### How to Access Audit Logs in Frontend

1. **Navigate to the Module**: Go to the module where you generated test data (e.g., Item Master, Customer Master)

2. **Find the Record**: Find the record using the transaction ID you specified when generating test data
   - For Item Master: Find item with the transaction ID (e.g., `3449`)
   - For Customer Master: Find customer with the transaction ID
   - For other modules: Navigate to the respective module and find the record

3. **Click Audit Logs Button**: Look for an "Audit Logs" or "View History" button/icon in the record action column
   - This button is typically located in the action toolbar or header section
   - The icon is usually a clock, history, or list icon

4. **View Logs in Modal**: The audit log modal will open displaying:
   - **Amended By**: User who made the change
   - **Amended Date & Time**: When the change occurred
   - **Narration**: Description of the change
   - **Type**: CRUD operation type (Create, Update, Delete)
   - **View**: Button to see detailed changes

#### Example: Viewing Item Master Audit Logs

**Step 1: Generate Test Data**
```bash
php artisan audit:generate-test-logs --count=10 --transaction-id=3449 --table=itemmaster
```

**Step 2: Process Queue Jobs**
```bash
php artisan queue:work database --tries=3
```

**Step 3: View in Frontend**
1. Navigate to **Item Master** module in the ERP frontend
2. Search for item with ID `3449`
3. Open the item detail view
4. Click the **Audit Logs** button (usually a clock/history icon)
5. The modal will display all 10 generated audit logs
---

### Method 2: Using the API Endpoint

The ERP system provides an API endpoint to query audit logs:

**Endpoint:** `POST /api/auditLogs`

**Request Body:**
```json
{
    "id": "3449",
    "module": "item",
    "companyId": 1,
    "fromDate": "2025-12-01 00:00:00",
    "toDate": "2025-12-31 23:59:59"
}
```

**Example using cURL:**
```bash
curl -X POST http://localhost:8000/api/auditLogs \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "id": "3449",
    "module": "item",
    "companyId": 1
  }'
```

**Module to Table Mapping:**
- `item` → `itemmaster`
- `customer` → `customermaster`
- `supplier` → `suppliermaster`
- `chartofaccounts` → `chartofaccounts`
- (See [AUDIT_LOG_DOCUMENTATION.md](./AUDIT_LOG_DOCUMENTATION.md) for full list)

### Method 3: Using LogsQL Queries (Direct Victoria Logs)

You can query Victoria Logs directly using LogsQL syntax through the `LokiService`:

**Example PHP Code:**
```php
use App\Services\LokiService;

$lokiService = new LokiService();

// Query logs for specific transaction ID and table
$query = '{env="erp-qa"} | json | transaction_id="3449" | table="itemmaster" | locale="en"';

$logs = $lokiService->queryLogsQL($query);
```

**Example LogsQL Queries:**

1. **Query by Transaction ID and Table:**
```logql
{env="erp-qa"} | json | transaction_id="3449" | table="itemmaster" | locale="en"
```

2. **Query with Date Range:**
```logql
{env="erp-qa"} | json | transaction_id="3449" | table="itemmaster" | date_time >= "2025-12-01" | date_time < "2025-12-31"
```

3. **Query by CRUD Type:**
```logql
{env="erp-qa"} | json | transaction_id="3449" | table="itemmaster" | crudType="U" | locale="en"
```

4. **Query by User:**
```logql
{env="erp-qa"} | json | user_name="John Doe" | table="itemmaster" | locale="en"
```

5. **Count Logs:**
```logql
{env="erp-qa"} | json | transaction_id="3449" | table="itemmaster" | stats count()
```

### Method 4: Using cURL (Direct Victoria Logs API)

You can query Victoria Logs directly using cURL:

```bash
curl -X POST "https://php-auditlogs-rnd.gears-int.com/select/logsql/query" \
  -u "vauth:YOUR_PASSWORD" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "query={env=\"erp-qa\"} | json | transaction_id=\"3449\" | table=\"itemmaster\" | locale=\"en\""
```

### Method 5: Using Grafana

1. **Connect to Grafana** and add Victoria Logs as a data source
2. **Use LogsQL queries** in the Explore view
3. **Create dashboards** for visual log analysis

---
**Maintained By**: ERP Development Team

