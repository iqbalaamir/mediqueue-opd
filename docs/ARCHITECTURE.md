# MediQueue OPD — Architecture

## Overview

MediQueue OPD is a multi-tenant-ready hospital outpatient queue management SaaS built on Laravel 12 with Blade views, Tailwind CSS 4, and vanilla JavaScript ES modules. Business logic lives in services; persistence goes through repositories; HTTP layers stay thin.

## Request Flow

```
HTTP Request
  → Form Request (validation)
  → Controller (thin)
  → Service (business rules)
  → Repository (persistence)
  → Eloquent Model / MySQL
```

Controllers validate input, authorize access, and delegate. Services orchestrate domain rules, transactions, and side effects. Repositories encapsulate query patterns and CRUD. Models define relationships, casts, and route key resolution via UUID.

## Directory Layout

```
app/
├── Domain/
│   └── Enums/              # AppointmentStatus, PaymentStatus, etc.
├── Exceptions/             # BookingException with render() → flash / JSON 422
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   └── Patient/
│   └── Requests/
│       ├── Admin/
│       └── Patient/
├── Models/
│   └── Concerns/
│       └── HasUuid.php
├── Providers/
│   └── RepositoryServiceProvider.php
├── Repositories/
│   ├── Contracts/
│   └── Eloquent/
└── Services/
    ├── Appointment/
    ├── Booking/
    ├── Notification/
    ├── Payment/
    ├── Qr/
    ├── Queue/
    └── Report/

resources/
├── views/
│   ├── layouts/            # guest, admin
│   ├── components/ui/      # toast, modal, skeleton, etc.
│   ├── patient/
│   └── admin/
└── js/
    └── modules/            # toast, modal, loading, http, queue-tracker, booking-schedule

config/
└── hospital.php            # branding, booking, queue, payment, notifications
```

## Layer Responsibilities

### Form Requests

Validate and normalize incoming data. Authorization hooks live here when route-specific.

### Controllers

Return views or JSON responses. No business rules. Catch domain exceptions only when not handled globally.

### Services

| Service | Responsibility |
|---------|----------------|
| `BookingService` | Book, confirm after payment, release/expire holds, available dates |
| `ConsultationFeeService` | Auto first-visit vs follow-up fee quote |
| `PaymentConfigResolver` | Doctor override → hospital defaults |
| `PaymentService` | Pending payment, demo complete/fail |
| `BookingOtpService` | Cache OTP, optional gate on store |
| `TokenGenerator` | Daily token sequence (e.g. `A-001`) |
| `QrCodeService` | Payload + SVG for appointment |
| `QueueService` | Desk operations, patient snapshot, ETA |
| `NotificationManager` | Channel drivers, event dispatch |
| `ReportService` | Dashboard and export aggregates |

### Repositories

Bound in `RepositoryServiceProvider`. One contract + Eloquent implementation per aggregate where practical. Keeps services testable and queries centralized.

### Enums

PHP 8.2 backed enums for statuses (`AppointmentStatus`, `PaymentStatus`, `QueueEntryStatus`, `DoctorStatus`, etc.). Used in models (casts), services, and Blade via `->value` or `->label()`.

## Public Identifiers

Public-facing routes use UUID route keys via the `HasUuid` trait on models exposed to patients. Internal admin may use numeric IDs where appropriate.

## Payment & Booking States

| Hospital config | Booking result |
|-----------------|----------------|
| Offline / online not required | Confirmed + token immediately; `payment_status = pending_collection` |
| Online full + required | Pending hold → pay full → confirm + token |
| Advance + required | Pending hold → pay advance → confirm + token; balance at hospital |
| Fee = 0 | Confirm immediately; payment not required |

Unpaid holds expire via `appointments:expire-unpaid` (scheduled every minute). Failure releases slot (`booked_count--`).

## Frontend

- **Blade only** — no Livewire, Inertia, or SPA frameworks
- **Tailwind CSS 4** — design tokens in `resources/css/app.css`
- **Vanilla JS modules** — toast, modal, loading overlay, HTTP helpers, queue polling
- Forms use **relative URLs** (`route(..., absolute: false)`) for CSRF compatibility across hosts
- Flash messages on guest pages via `data-flash-success` / `data-flash-error` on `<body>`

## Testing

Feature tests use SQLite `:memory:`. Critical flows: offline booking, online payment, payment fail/slot release, follow-up fee, expire holds, duplicate/resume pending, admin CRUD, queue desk actions.

## Module Build Order

1. Foundation (layouts, design system, config, provider)
2. Database & models
3. Patient booking wizard
4. Live queue
5. Notifications
6. Admin master CRUD
7. Slots & appointments admin
8. Dashboard, reports, settings
9. Auth & QA
