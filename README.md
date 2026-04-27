# Modacore — Fashion Rental Management System

Modacore is an internal ERP for managing the end-to-end lifecycle of fashion item rentals — from stock cataloguing through customer bookings, order fulfilment, and return tracking.

---

## Table of Contents

1. [Overview](#overview)
2. [Tech Stack](#tech-stack)
3. [Core Modules](#core-modules)
4. [Data Model](#data-model)
5. [Booking & Order Flow](#booking--order-flow)
6. [Availability & Double-Booking Prevention](#availability--double-booking-prevention)
7. [Customer Management](#customer-management)
8. [Payments](#payments)
9. [Setup](#setup)

---

## Overview

Staff log in and use the system to:

- Catalogue inventory items (gowns, suits, accessories) with size/colour variants and rental prices.
- Register customers and store their contact and ID details.
- Create bookings that reserve one or more items for a specified hire window.
- Convert confirmed bookings into active orders when the customer collects.
- Record returns and mark orders complete.
- Track payments against each booking.

The system enforces **hard availability rules** so the same item/variant can never be double-booked across overlapping hire dates.

---

## Tech Stack

| Layer | Package |
|---|---|
| Framework | Laravel 13 |
| UI | Livewire 4 + Flux UI v2 |
| Styling | Tailwind CSS v4 |
| Auth | Laravel Fortify |
| Database | SQLite (dev) / MySQL (prod) |
| Testing | Pest 4 |

---

## Core Modules

### 1. Inventory
Manage the physical stock available for hire.

- **Categories** — group items (e.g. Evening Wear, Bridal, Suits).
- **Items** — each item has a name, base rental price, SKU, and active status.
- **Variants** — size/colour combinations of an item, each with an optional price override.

### 2. Customers
A customer is any person who hires items. Customers are separate from staff (users).

- Name, email, phone, physical address.
- National ID / passport number for security deposit tracking.
- Hire history and outstanding balances visible per customer.

### 3. Bookings
A booking is a **reservation** — it locks items for a customer during a date window before the order is physically fulfilled.

- Each booking has a unique auto-generated reference number (`BK-YYYYMMDD-XXXX`).
- A booking can hold multiple items/variants with individual quantities and prices.
- Statuses: `draft` → `confirmed` → `active` → `completed` | `cancelled`.

### 4. Orders
An order represents a booking that has been **physically handed over** to the customer.

- Created from a confirmed booking at the point of collection.
- Tracks actual collection date and expected return date.
- Return is recorded per item (allows partial returns).

### 5. Payments
Payments are recorded against a booking (not the order directly) so partial and advance payments are supported.

- Methods: cash, card, mobile money.
- A booking's financial status: `unpaid` | `partial` | `paid`.
- Security deposits are tracked separately and refunded on completion.

---

## Data Model

```
users                       (staff who operate the system)
├── id, name, email, password

customers
├── id, name, email, phone, address
├── id_number               (national ID / passport — for deposit)
├── notes
└── created_by → users.id

inventory_categories
├── id, name, description
└── user_id → users.id

inventory_items
├── id, name, description, sku
├── base_rental_price
├── is_active
└── category_id → inventory_categories.id

inventory_variants
├── id, size, color, sku
├── rental_price            (overrides base if set)
└── inventory_item_id → inventory_items.id

bookings
├── id, booking_number      (unique: BK-YYYYMMDD-XXXX)
├── customer_id → customers.id
├── hire_from (date)
├── hire_to   (date)
├── status    (draft|confirmed|active|completed|cancelled)
├── notes, total_amount
└── created_by → users.id

booking_items
├── id
├── booking_id → bookings.id
├── inventory_item_id → inventory_items.id
├── inventory_variant_id → inventory_variants.id (nullable)
├── quantity
├── unit_price              (snapshot of price at time of booking)
└── subtotal

payments
├── id
├── booking_id → bookings.id
├── amount, payment_method  (cash|card|mobile_money)
├── reference, notes
├── is_deposit              (boolean — security deposit flag)
├── paid_at
└── created_by → users.id
```

---

## Booking & Order Flow

```
1. CUSTOMER REGISTRATION
   Staff creates/finds a customer record.

2. CREATE BOOKING (status: draft)
   Staff selects:
     - Customer
     - Hire from / hire to dates
     - One or more items + variants + quantities
   System checks availability for every line item before saving.

3. CONFIRM BOOKING (status: confirmed)
   Staff reviews and confirms the booking.
   Items are now considered reserved — no other booking can overlap.
   Booking reference is issued.

4. COLLECT / HAND OVER (status: active)
   On the day of collection, staff converts the booking to an active order.
   Actual collection date and expected return date are recorded.
   A receipt / booking sheet can be printed.

5. RECORD PAYMENT
   Staff records payments against the booking at any stage (deposit on
   confirmation, balance on collection, etc.).

6. RETURN (status: completed)
   Staff records item returns (can be per-item for partial returns).
   Security deposit is flagged for refund.
   Booking status moves to completed.
   Items become available again for new bookings.

7. CANCELLATION
   Any booking can be cancelled before status = active.
   Cancelled bookings free all reserved items immediately.
```

---

## Availability & Double-Booking Prevention

The rule: **an item (or variant) cannot appear in two confirmed/active bookings whose hire windows overlap.**

### Overlap condition

Two date ranges `[A_from, A_to]` and `[B_from, B_to]` overlap when:

```
A_from <= B_to  AND  A_to >= B_from
```

### Enforcement layers

**Layer 1 — Application check (before saving a booking item)**

Before inserting a `booking_items` row the system runs:

```sql
SELECT COUNT(*)
FROM booking_items bi
JOIN bookings b ON b.id = bi.booking_id
WHERE bi.inventory_item_id = :item_id
  AND (bi.inventory_variant_id = :variant_id OR :variant_id IS NULL)
  AND b.status IN ('confirmed', 'active')
  AND b.id != :current_booking_id
  AND b.hire_from <= :hire_to
  AND b.hire_to   >= :hire_from
```

If the count > 0 the item is unavailable and a validation error is returned to the user.

**Layer 2 — Unique database constraint**

A partial unique index enforces the rule at the database level as a safety net against race conditions:

```sql
-- Enforced via a check in a database transaction with SELECT ... FOR UPDATE
```

**Layer 3 — Status gate**

Only bookings with status `confirmed` or `active` lock availability.
`draft` and `cancelled` bookings do not block inventory.

### Availability calendar

The booking form shows a per-item availability calendar highlighting:
- 🟢 Available
- 🔴 Booked (confirmed/active)
- 🟡 Draft (soft reservation — may be released)

---

## Customer Management

Customers are independent of system users (staff). Key features:

- **Registration** — staff registers a customer with name, contact, and ID details.
- **Customer profile** — full hire history, active bookings, payment balance.
- **Search** — searchable by name, email, phone, or ID number.
- **ID verification** — ID number stored for security deposit tracking and dispute resolution.

---

## Payments

| Scenario | Behaviour |
|---|---|
| Advance deposit | Recorded against booking before collection |
| Full payment on collection | Recorded when order goes active |
| Partial payment | Allowed; balance tracked automatically |
| Security deposit | Flagged separately; shown as refundable on completion |
| Overpayment | Change amount shown on payment form |

`booking.total_amount` is calculated from `SUM(booking_items.subtotal)`.
`booking.amount_paid` is calculated from `SUM(payments.amount WHERE is_deposit = false)`.
`booking.deposit_paid` is calculated from `SUM(payments.amount WHERE is_deposit = true)`.

---

## Setup

```bash
# Install dependencies
composer install
npm install

# Environment
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate

# Run (via Herd — site is always at https://modacore.test)
npm run dev
```

---

## Planned Modules (Roadmap)

| Module | Status |
|---|---|
| Inventory (items + categories + variants) | ✅ Done |
| Customer registration & profiles | 🔲 Planned |
| Bookings (create, confirm, cancel) | 🔲 Planned |
| Availability checker | 🔲 Planned |
| Order fulfilment (collect + return) | 🔲 Planned |
| Payments & deposits | 🔲 Planned |
| Booking sheet / receipt print view | 🔲 Planned |
| Dashboard (active orders, due returns, revenue) | 🔲 Planned |
