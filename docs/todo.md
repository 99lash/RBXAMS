# RBXAMS DEVELOPMENT TODOs

# Roblox Asset Monitoring System - To-Do List

## ✅ Completed Tasks

- [x] **Authentication:** Single User Login (Frontend & Backend)
- [x] **Core Features (Backend):**
    - [x] Accounts Management (Create, Read, Update, Delete)
    - [x] Daily Transaction Logging (Buy/Sell Logic)
- [x] **Initial UI:**
    - [x] Login Page
    - [x] Main Layout (Header, Sidebar, Guide Page)
    - [x] Light & Dark Mode Theme

---

## サーバー Backend To-Do List (Server-Side Logic & API)

This covers the logic needed on the server to support the UI features.

### Dashboard API
- [ ] Create endpoint (`/api/dashboard/summary`) for summary card data (Total Robux Bought/Sold, Profit) from the `daily_summary` table. [cite: 21]
- [ ] Create endpoint (`/api/dashboard/charts`) for chart data (e.g., daily profit for the last 30 days). [cite: 45]

### Accounts API Enhancements
- [ ] Update `findAll` endpoint to support **pagination**. [cite: 58]
- [ ] Add logic for **searching** by account name. [cite: 60]
- [ ] Add logic for **filtering** by status and date range. [cite: 61]

### Daily Activity Summary API
- [ ] Create endpoint (`/api/summary/daily`) for a paginated list of daily summaries. [cite: 94]
- [ ] Implement **date range filtering** for the daily summary endpoint. [cite: 95]

### Data Export Logic
- [ ] Create endpoint (`/api/accounts/export?format=csv`) to generate and download a CSV/Excel file of accounts. [cite: 121, 122]
- [ ] Create endpoint (`/api/summary/export?format=pdf`) to generate and download a CSV/PDF file of the daily summary. [cite: 123, 124]

### Features & Security
- [ ] Implement "Forgot Password" logic (token generation, email, password update). [cite: 117]
- [ ] (Optional) Create a `manifest.json` and basic `service-worker.js` to be served by the backend for PWA support. [cite: 151]
- [ ] (Optional) Create a scheduled task/cron job to check for accounts nearing their `unpend_date` for notifications. [cite: 4]

---

## 🖥️ Frontend To-Do List (UI/UX)

This covers building the user interface and connecting it to the backend API.

### Dashboard Page
- [ ] Design the Dashboard UI with four summary cards (Robux Bought, Sold, Invested, Profit). [cite: 21]
- [ ] Fetch data from `/api/dashboard/summary` and display it in the cards.
- [ ] Integrate a charting library (e.g., Chart.js) and display data from `/api/dashboard/charts`. [cite: 45]

### Account Management UI
- [ ] Create an **"Add Account" page** or modal with a form for new accounts. [cite: 47]
- [ ] Design the **"Manage Accounts" page** with a table for all accounts. [cite: 57, 58]
- [ ] Implement the data table to fetch from the Accounts API, including **pagination controls**. [cite: 58]
- [ ] Add a **search input** and **filter dropdowns** (by status, date). [cite: 60, 61]
- [ ] Create **"Edit Account"** and **"Delete Account"** functionality with confirmation modals, connected to the API. [cite: 77, 92]
- [ ] Add an "Export" button to trigger the CSV/Excel file download. [cite: 121]

### Daily Activity Summary Page
- [ ] Design the page to display daily summary data in a table. [cite: 93, 94]
- [ ] Add a **date range picker** for filtering data. [cite: 95]
- [ ] Add an "Export" button for CSV/PDF downloads. [cite: 96]

### General UI/UX
- [ ] Implement the UI for the **"Forgot Password"** flow. [cite: 117]
- [ ] Create a UI component for **in-app notifications** (e.g., toast messages for success/error). [cite: 110, 111]
- [ ] Make all pages **fully responsive** to be mobile-friendly. [cite: 17, 149]
- [ ] (Optional) Integrate PWA features into the main HTML to make the web app "installable." [cite: 118, 119]