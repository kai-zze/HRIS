# KM100 Group Human Resource Information System (HRIS)

A multi-entity, enterprise-grade Human Resource Information System custom engineered for the **KM100 Philippine Corporate Group**:
1. **KM100 Skills and Training Center, Inc.** (`KM100-STC`)
2. **Knoah Mari** (`KNOAH`)
3. **Red Goose** (`REDGOOSE`)
4. **STC** (`STC`)
5. **PETC** (`PETC`)

---

## Architecture & Technology Stack

- **Backend Framework:** Laravel 13 + PHP 8.3+
- **Administrative & Self-Service UI:** Filament v5 with Livewire 3
- **Design System:** KM100 "Operate Mode" — High-contrast Industrial Orange (`#F2851D`/`#C2510A`) with Graphite & Charcoal Neutrals (`#3B3B3D`/`#18181B`) and tabular data layouts
- **Database:** MySQL 8.4 (with multi-tenant entity scoping via global query scopes and traits)
- **Document Generation:** Barryvdh DomPDF (`barryvdh/laravel-dompdf`) for printable 201 files and confidential payslips
- **Batch Exporting:** Maatwebsite Excel & Native CSV streaming for high-speed report downloads
- **Access Control & Auditing:** Spatie Laravel Permission (RBAC) + Spatie Activitylog (immutable audit trail)

---

## 7 Core Implementation Phases

### Phase 1: Multi-Company Foundation & RBAC
- Multi-company entity isolation powered by `BelongsToEntity` trait and `EntityScope`.
- Global topbar entity switcher allowing Super Admins to seamlessly toggle between the 5 operating entities.
- Core organization models: `Entity`, `Department`, `Position`, `User`, and `Role`.
- Role-Based Access Control: Super Admin, HR Admin, HR Staff, Department Manager, and Employee.

### Phase 2: Core HR (Employee 201 File Management)
- Complete Philippine 201 employee records with auto-generated sequential IDs (`{ENTITY}-2026-XXXX`).
- Multi-tabbed profile forms: Personal Details, Statutory IDs, Employment Info, and Emergency Contacts.
- **Data Privacy Act (RA 10173) Compliance:** Mandatory AES-256-CBC encryption at rest for TIN, SSS, PhilHealth, Pag-IBIG, and bank account numbers; dynamic masking with asterisks in UI tables.
- Infolist 201 view with DomPDF printable download and Excel export.

### Phase 3: Time, Attendance & Philippine Leave Management
- Customizable work schedules with grace periods (minutes) and flexible break configurations.
- Biometric Daily Time Record (DTR) CSV import action.
- `AttendanceService`: automatic calculation of tardiness (minutes), undertime, regular hours, and overtime.
- `LeaveBalanceService`: enforces Philippine statutory leave credits (5 days Service Incentive Leave / Vacation Leave, 5 days Sick Leave, Maternity, Paternity, and Solo Parent Leave).
- Philippine nationwide holiday calendar engine and interactive visual leave calendar widget.

### Phase 4: Employee Self-Service Panel (`/portal`)
- Dedicated self-service panel located at `/portal`.
- My Profile page with emergency contact repeater and masked personal identifiers.
- My Leave Requests resource with live balance validation (cannot submit requests exceeding remaining credits).
- My Daily Time Records with tardiness color codes and date filtering.
- My Overtime Requests with justification and status tracking.
- Broadcast Company Announcements with urgency badges (Urgent, Normal, Low).

### Phase 5: Recruitment ATS, 5-Factor Ranking, Discipline & Training
- **Recruitment ATS:** Job openings and applicant pipeline with 1-click `convertToEmployee` action generating 201 files and 6 onboarding checklist items.
- **5-Factor Performance Ranking Engine (`PerformanceRankingService`):**
  - Evaluation Score: 50%
  - Attendance & Punctuality: 20%
  - Training Accomplishment: 15%
  - Disciplinary Record: 10%
  - Commendations: 5%
- Performance Leaderboard with Gold, Silver, and Bronze badges, HR manual score adjustment with mandatory audit reasons, and Excel export.
- **Discipline Management:** Notice to Explain (NTE), administrative hearings, sanctions (Written Reprimand, Suspension, Termination), and penalty deductions.
- **Training Programs:** Training catalog with certificate upload, validity tracking, and expiry notifications.

### Phase 6: Philippine Payroll & Compensation Engine
- **Statutory Rate Schedules (Database-Driven, Fully Editable):**
  - **SSS 2026 Schedule:** 63 Monthly Salary Credit (MSC) brackets (₱4,000 to ₱35,000) computing regular EE (4.5%) / ER (9.5%), EC fund, and mandatory WISP/MPF for MSC > ₱20,000.
  - **PhilHealth 2026 Schedule:** 5.0% premium split equally (2.5% EE / 2.5% ER) with ₱10,000 floor and ₱100,000 ceiling.
  - **Pag-IBIG (HDMF) 2026 Circular:** Mandatory ₱200 EE / ₱200 ER based on ₱10,000 max compensation cap.
  - **BIR TRAIN Law Withholding Tax:** Semi-monthly graduated tax brackets. Statutory Minimum Wage Earners are completely tax-exempt.
- Semi-monthly cutoff payroll processor (`PayrollComputationService`) integrating DTR late/undertime/absent deductions and OT multipliers (regular OT @ 125%, rest day @ 130%, regular holiday @ 200%).
- **13th Month Pay Service (`ThirteenthMonthService`):** Implements Presidential Decree No. 851 (total basic pay / 12) with ₱90,000 tax-exempt ceiling tracking.
- **Employee Loans:** SSS salary loans, Pag-IBIG multi-purpose loans, and company cash advances with automatic cutoff deductions and balance amortization.
- BDO/BPI standard batch payroll CSV export.
- Confidential DomPDF printable payslips and employee portal vault download.

### Phase 7: Reports, Audit Log Viewer, Analytics & Settings
- **Reports & Compliance Center (`/admin/reports-center`):**
  - Headcount & Workforce Demographics Report (CSV/Excel)
  - Biometric Attendance & Tardiness Summary Report (CSV/Excel)
  - Statutory Remittance Schedule (SSS, PhilHealth, Pag-IBIG, BIR TRAIN)
  - Leave Utilization & Forfeiture Audit Report (CSV/Excel)
- **Audit Trail & Activity Log (`/admin/activity-logs`):** Spatie Activitylog viewer with JSON diff inspection showing old vs. new values for RA 10173 accountability.
- **Executive Dashboard Analytics & Charts:**
  - Group Headcount by Operating Entity (Doughnut Chart)
  - Monthly Payroll & Labor Cost Summary (Bar Chart)
  - Attendance & Tardiness Trend (Line Chart)
- **System Settings (`/admin/system-settings`):** Centralized Philippine labor and tax parameters.

---

## Quick Start & Installation

### Prerequisites
- PHP 8.3+ (e.g. Laragon or PHP binary)
- Composer
- MySQL 8.4+

### Installation Commands
```powershell
# 1. Start MySQL daemon (if using local Laragon MySQL)
& "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqld.exe" --defaults-file="C:\laragon\bin\mysql\mysql-8.4.3-winx64\my.ini"

# 2. Add PHP to session PATH
$env:PATH = "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64;" + $env:PATH

# 3. Install dependencies & initialize database
composer install
php artisan migrate:fresh --seed

# 4. Start Laravel server
php artisan serve
```

---

## Panel URLs & Direct Access

> **Note:** Direct dashboard access is currently enabled (temporary sign-in bypass). Navigating to either URL opens the respective dashboard automatically without authentication prompts.

- **HR Admin Panel:** [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin) (or [http://127.0.0.1:8000/](http://127.0.0.1:8000/))
- **Employee Self-Service Portal:** [http://127.0.0.1:8000/portal](http://127.0.0.1:8000/portal)
- **Reports Center:** [http://127.0.0.1:8000/admin/reports-center](http://127.0.0.1:8000/admin/reports-center)
- **Audit Trail Logs:** [http://127.0.0.1:8000/admin/activity-logs](http://127.0.0.1:8000/admin/activity-logs)

### Default User Credentials (When Sign-In is Re-enabled)
All accounts use the standard password: `Password123!`

| Role | Scope | Email | Default Password |
| :--- | :--- | :--- | :--- |
| **Super Admin** | Group Wide (All 5 Entities) | `superadmin@km100group.ph` | `Password123!` |
| **HR Admin** | KM100 Skills and Training Center | `hr.km100@km100group.ph` | `Password123!` |
| **HR Admin** | Knoah Mari | `hr.knoah@km100group.ph` | `Password123!` |
| **HR Admin** | Red Goose | `hr.redgoose@km100group.ph` | `Password123!` |
| **HR Admin** | STC | `hr.stc@km100group.ph` | `Password123!` |
| **HR Admin** | PETC | `hr.petc@km100group.ph` | `Password123!` |

---

## Automated Test Suites

The application includes 47 automated feature tests across all 7 phases with 100% pass rate:

```powershell
$env:PATH = "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64;" + $env:PATH
php artisan test
```

### Breakdown by Phase:
- `Phase1FoundationTest.php`: Multi-company scoping, switcher, and RBAC permissions.
- `Phase2CoreHrTest.php`: 201 file management, encrypted statutory IDs, and sequential employee numbering.
- `Phase3TimeAttendanceLeaveTest.php`: Biometric DTR parsing, tardiness calculations, and leave balance validation.
- `Phase4EmployeeSelfServiceTest.php`: Employee portal, profile, leave requests, and announcements.
- `Phase5RecruitmentPerformanceDisciplineTrainingTest.php`: ATS candidate conversion, 5-factor ranking, disciplinary actions, and training certificates.
- `Phase6PayrollTest.php`: SSS/PhilHealth/Pag-IBIG tables, TRAIN Law tax withholding, loan deductions, bank CSV export, and PDF payslips.
- `Phase7ReportsAnalyticsAuditTest.php`: Activitylog audit trail, executive chart data, and report CSV exports.
