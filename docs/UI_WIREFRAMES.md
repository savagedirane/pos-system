# POS System - UI/UX Wireframes & Design Specifications

## Design Philosophy

**Principles**:
1. **Simplicity**: Intuitive interface minimizing training needs
2. **Efficiency**: Keyboard shortcuts and quick access to frequent operations
3. **Consistency**: Unified design language across all pages
4. **Accessibility**: WCAG 2.1 AA compliance
5. **Responsiveness**: Works seamlessly on desktop and tablet devices

---

## Color Scheme

**Primary Colors**:
```css
--primary-color: #2C3E50;    /* Dark blue-gray */
--secondary-color: #3498DB;  /* Bright blue */
--success-color: #27AE60;    /* Green */
--warning-color: #F39C12;    /* Orange */
--danger-color: #E74C3C;     /* Red */
--info-color: #16A085;       /* Teal */
--light-bg: #ECF0F1;         /* Light gray */
--dark-text: #2C3E50;        /* Dark text */
--light-text: #7F8C8D;       /* Gray text */
```

**Usage**:
- Primary: Navigation, headers, primary actions
- Secondary: Highlights, important information
- Success: Confirmations, successful operations
- Warning: Cautions, important notices
- Danger: Deletions, critical actions
- Info: Informational messages

---

## Typography

**Font Stack**:
```css
Font Family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
Fallback: system-ui, -apple-system, sans-serif;
```

**Sizing Scale**:
```
H1: 32px (page titles)
H2: 24px (section headers)
H3: 18px (subsections)
H4: 16px (labels, minor headers)
Body: 14px (standard text)
Small: 12px (metadata, captions)
Monospace: 13px (code, technical data)
```

**Weights**:
- Regular (400): Body text
- Medium (500): Labels, important text
- Bold (700): Headers, emphasis

---

## 1. Dashboard / Home Page

### Purpose
Provide executives and managers with at-a-glance business metrics and quick access to core functions.

### Layout Structure

```
┌─────────────────────────────────────────────────────────────┐
│  HEADER: POS Logo | User Menu | Notifications | Help        │
├─────────────────────────────────────────────────────────────┤
│  SIDEBAR: Navigation Menu (collapsible)                      │
│  ├─ Dashboard                                               │
│  ├─ Sales                                                   │
│  ├─ Inventory                                               │
│  ├─ Customers                                               │
│  ├─ Products                                                │
│  ├─ Purchasing                                              │
│  ├─ Reports                                                 │
│  ├─ Chatbot                                                 │
│  └─ Settings                                                │
├─────────────────────────────────────────────────────────────┤
│ MAIN CONTENT AREA:                                          │
│ ┌───────────────────────────────────────────────────────┐   │
│ │ Welcome Back, [User Name]!                            │   │
│ │ Today's Date: [Date] | System Status: [Status]       │   │
│ └───────────────────────────────────────────────────────┘   │
│                                                              │
│ ┌──────────────────┐ ┌──────────────────┐ ┌─────────────┐  │
│ │ Today's Sales    │ │ Total Revenue    │ │ Transactions│  │
│ │ $2,345.50        │ │ $12,456.89       │ │ 156         │  │
│ │ 23 transactions  │ │ Last 30 days     │ │ This hour   │  │
│ └──────────────────┘ └──────────────────┘ └─────────────┘  │
│                                                              │
│ ┌──────────────────┐ ┌──────────────────┐ ┌─────────────┐  │
│ │ Low Stock Items  │ │ Top Products     │ │ Inventory   │  │
│ │ 12 items need    │ │ 1. ProductA: 145 │ │ Total Value │  │
│ │ reordering       │ │ 2. ProductB: 132 │ │ $45,320.50  │  │
│ └──────────────────┘ └──────────────────┘ └─────────────┘  │
│                                                              │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ Sales Trend (Last 7 Days) - Line Chart                 │ │
│ │ [Chart visualization here]                             │ │
│ └─────────────────────────────────────────────────────────┘ │
│                                                              │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ Recent Transactions                                    │ │
│ │ ┌────┬──────────┬──────────┬─────────┬──────────────┐ │ │
│ │ │ ID │ Customer │ Amount   │ Time    │ Status       │ │ │
│ │ ├────┼──────────┼──────────┼─────────┼──────────────┤ │ │
│ │ │ 12 │ John Doe │ $234.50  │ 10:32AM │ Completed    │ │ │
│ │ │ 11 │ Jane Sm  │ $456.75  │ 10:15AM │ Completed    │ │ │
│ │ │ 10 │ Guest    │ $123.45  │ 10:05AM │ Completed    │ │ │
│ │ └────┴──────────┴──────────┴─────────┴──────────────┘ │ │
│ └─────────────────────────────────────────────────────────┘ │
│                                                              │
│ Quick Action Buttons:                                       │
│ [New Sale] [Manage Inventory] [View Reports] [Add Product]  │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### Components Used
- Bootstrap Cards for metric displays
- Chart.js for sales trend visualization
- Responsive Grid (12-column)
- Color-coded status indicators
- Quick action buttons

### Interactive Elements
- Collapsible sidebar toggle
- User dropdown menu
- Notification bell with count
- Date/time widget with timezone
- Quick stat cards with drill-down links

---

## 2. Sales / Point of Sale (POS) Screen

### Purpose
Primary interface for cashiers to process customer transactions efficiently.

### Layout Structure

```
┌─────────────────────────────────────────────────────────────┐
│ HEADER: POS Mode | Sale #SO-20240117-001 | Cashier: John    │
├─────────────────────────────────────────────────────────────┤
│ ┌──────────────────────────────┐  ┌──────────────────────┐  │
│ │ PRODUCT SEARCH              │  │ SHOPPING CART        │  │
│ │ ┌──────────────────────────┐ │  │ ┌─────────────────┐  │  │
│ │ │ [Search products/barcode]│ │  │ │ Item│Qty│Price  │  │  │
│ │ │ [By code, name, SKU]    │ │  │ ├─────────────────┤  │  │
│ │ └──────────────────────────┘ │  │ │ A12│ 2│$24.99 │  │  │
│ │                              │  │ │ B45│ 1│$45.50 │  │  │
│ │ QUICK CATEGORIES:            │  │ │ C78│ 3│$15.00 │  │  │
│ │ [Electronics] [Groceries]    │  │ │    │  │       │  │  │
│ │ [Clothing] [Other]           │  │ │ Delete|Qty ▲▼   │  │  │
│ │                              │  │ │                 │  │  │
│ │ RECENT PRODUCTS:             │  │ │ Subtotal: $324.99│  │  │
│ │ [Product A] [Product B]      │  │ │ Tax (8%):  $25.99│  │  │
│ │ [Product C] [Product D]      │  │ │ Discount:  $-10  │  │  │
│ │ [Product E] [Product F]      │  │ │                 │  │  │
│ │                              │  │ │ TOTAL: $340.98   │  │  │
│ │ Top 10 Best Sellers:         │  │ └─────────────────┘  │  │
│ │ ┌────────┬─────┬────────┐   │  │                       │  │
│ │ │Name    │Qty  │Price   │   │  │ [Clear Cart]         │  │
│ │ ├────────┼─────┼────────┤   │  │ [Save Sale]          │  │
│ │ │Prod-A  │542  │$12.99  │   │  │ [Suspend Sale]       │  │
│ │ │Prod-B  │438  │$24.50  │   │  │                       │  │
│ │ │Prod-C  │367  │$8.99   │   │  │ CUSTOMER LOOKUP      │  │
│ │ │Prod-D  │289  │$34.99  │   │  │ ┌─────────────────┐  │  │
│ │ └────────┴─────┴────────┘   │  │ │[Enter phone/name]│  │  │
│ │                              │  │ │[+ New Customer]  │  │  │
│ │                              │  │ │                 │  │  │
│ │                              │  │ │Points: 345      │  │  │
│ │                              │  │ └─────────────────┘  │  │
│ └──────────────────────────────┘  │                       │  │
│                                    │ PAYMENT METHOD       │  │
│                                    │ [Cash] [Card] [Check]│  │
│                                    │ [Mobile] [Other]     │  │
│                                    │                       │  │
│                                    │ DISCOUNT             │  │
│                                    │ [Percentage] [Fixed]│  │
│                                    │ Amount: [___________]│  │
│                                    │                       │  │
│                                    │ ┌─────────────────┐  │  │
│                                    │ │ [COMPLETE SALE] │  │  │
│                                    │ │ [PRINT RECEIPT] │  │  │
│                                    │ │ [EMAIL RECEIPT] │  │  │
│                                    │ └─────────────────┘  │  │
│                                    │ [Void Transaction]   │  │
│                                    │                       │  │
└─────────────────────────────────────────────────────────────┘
```

### Components Used
- Product search with autocomplete
- Dynamic shopping cart
- Real-time calculation
- Customer profile lookup
- Payment method selector
- Discount calculator
- Receipt printer integration

### Interactive Elements
- Barcode scanning input
- Quick quantity adjustment (spinner)
- Remove item from cart
- Apply discount percentage or fixed amount
- Search filters by category
- Recent/favorite products quick access

### Keyboard Shortcuts
- `Enter`: Complete sale
- `Esc`: Clear search
- `Ctrl+P`: Print receipt
- `Ctrl+S`: Suspend sale
- `Tab`: Move to next field

---

## 3. Inventory Management Screen

### Purpose
Track stock levels, manage inventory adjustments, and monitor reorder points.

### Layout Structure

```
┌─────────────────────────────────────────────────────────────┐
│ INVENTORY MANAGEMENT                                        │
├─────────────────────────────────────────────────────────────┤
│ Filter & Search:                                            │
│ [Search products]  [Category ▼]  [Low Stock Only] [Refresh] │
│ [Export CSV]  [Print Report]                                │
│                                                              │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ Inventory Summary                                       │ │
│ │ Total Products: 2,345  |  Low Stock: 23  |  Total Value: │ │
│ │ $125,450.75                                             │ │
│ └─────────────────────────────────────────────────────────┘ │
│                                                              │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ PRODUCT INVENTORY TABLE                                 │ │
│ │ ┌─────┬───────────┬────────┬─────┬────────┬────────────┐ │
│ │ │ SKU │ Product   │Category│Stock│Reorder │ Value      │ │
│ │ ├─────┼───────────┼────────┼─────┼────────┼────────────┤ │
│ │ │A001 │Product A  │Electr. │ 45  │   10   │ $449.55    │ │
│ │ │ ⚠ LOW         (Action buttons)                         │ │
│ │ │     │Adjust qty │ Reorder │ View                      │ │
│ │ │     │ [_____]   │ [____]  │Details                    │ │
│ │ │     │                                                 │ │
│ │ │B002 │Product B  │Grocers │ 5   │   10   │ $90.45     │ │
│ │ │ ⚠ CRITICAL    (Action buttons)                        │ │
│ │ │     │           │                                     │ │
│ │ │C003 │Product C  │Clothing│245  │   50   │ $2,345.00  │ │
│ │ │ ✓ OK                                                  │ │
│ │ │                                                       │ │
│ │ │D004 │Product D  │Electr. │ 0   │   20   │ $0.00      │ │
│ │ │ ✗ OUT OF STOCK (Red highlight)                        │ │
│ │ │     │                                                 │ │
│ │ └─────┴───────────┴────────┴─────┴────────┴────────────┘ │
│ │ Rows per page: 10 ▼  | Showing 1-10 of 2,345            │ │
│ └─────────────────────────────────────────────────────────┘ │
│                                                              │
│ QUICK ACTIONS:                                              │
│ [Add Product] [Import Stock] [Stock Adjustment] [Sync]      │
│                                                              │
│ Recent Adjustments:                                         │
│ ┌──────────┬─────────┬──────────┬──────────────────┐        │
│ │ Date     │ Product │ Type     │ Quantity Changed │        │
│ ├──────────┼─────────┼──────────┼──────────────────┤        │
│ │ 17 Jan   │ ProdA   │ Damage   │ -5               │        │
│ │ 16 Jan   │ ProdB   │ Purchase │ +100             │        │
│ └──────────┴─────────┴──────────┴──────────────────┘        │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### Components Used
- Searchable data table with sorting
- Status indicators (✓ OK, ⚠ LOW, ✗ OUT)
- Quantity adjustment modal dialog
- Pagination controls
- Export/Import functionality
- Color-coded row highlighting
- Action buttons within table rows

### Interactive Elements
- Inline quantity editor
- Quick reorder button
- Product detail drawer/modal
- Adjustment reason selector
- Bulk quantity adjustment
- Filter by status (OK, Low, Out)

---

## 4. Product Management Screen

### Purpose
Manage product database with CRUD operations, categorization, and supplier mapping.

### Layout Structure

```
┌─────────────────────────────────────────────────────────────┐
│ PRODUCT MANAGEMENT                                          │
├─────────────────────────────────────────────────────────────┤
│ [+ Add New Product] [Import] [Export] [Inactive]            │
│ Search: [__________________]  Category: [Electronics ▼]    │
│                                                              │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ PRODUCTS GRID / LIST VIEW TOGGLE                        │ │
│ │ ┌──────────────────┐ ┌──────────────────┐              │ │
│ │ │ [Product Image]  │ │ [Product Image]  │ ...         │ │
│ │ │ Product Name     │ │ Product Name     │              │ │
│ │ │ SKU: A001        │ │ SKU: B002        │              │ │
│ │ │ Price: $24.99    │ │ Price: $45.50    │              │ │
│ │ │ Stock: 45        │ │ Stock: 5 ⚠       │              │ │
│ │ │                  │ │                  │              │ │
│ │ │ [Edit] [Delete]  │ │ [Edit] [Delete]  │              │ │
│ │ └──────────────────┘ └──────────────────┘              │ │
│ │ ┌──────────────────┐                                   │ │
│ │ │ [Product Image]  │                                   │ │
│ │ │ Product Name     │                                   │ │
│ │ │ SKU: C003        │                                   │ │
│ │ │ Price: $8.99     │                                   │ │
│ │ │ Stock: 245       │                                   │ │
│ │ │                  │                                   │ │
│ │ │ [Edit] [Delete]  │                                   │ │
│ │ └──────────────────┘                                   │ │
│ └─────────────────────────────────────────────────────────┘ │
│ Showing 1-12 of 2,345 | Rows: 12▼  [< Page 1 of 196 >]    │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### Add/Edit Product Modal

```
┌──────────────────────────────────────────────┐
│ ADD NEW PRODUCT                        [X]   │
├──────────────────────────────────────────────┤
│ BASIC INFORMATION                           │
│ Product Name: [_____________________]       │
│ SKU: [__________________]                   │
│ Category: [Electronics ▼]                   │
│ Description: [_____________________]        │
│ [_____________________________]              │
│                                              │
│ PRICING & COST                              │
│ Cost Price: $[__________] | Markup: [%]    │
│ Selling Price: $[__________]                │
│ Calculate selling price from cost + markup  │
│                                              │
│ INVENTORY                                   │
│ Initial Quantity: [__________]              │
│ Reorder Level: [__________]                 │
│ Reorder Quantity: [__________]              │
│                                              │
│ SUPPLIER & BARCODE                         │
│ Supplier: [Search/Select ▼]                │
│ Barcode: [__________________]               │
│ [Generate] [Scan]                           │
│                                              │
│ PRODUCT IMAGE                              │
│ [Choose File...]  [Preview Image]           │
│                                              │
│ [Save Product]  [Cancel]                    │
│                                              │
└──────────────────────────────────────────────┘
```

### Components Used
- Product card grid/list view toggle
- Image upload with preview
- Category selector dropdown
- Price calculation helper
- Barcode generator and scanner
- Supplier autocomplete search
- Modal dialog for add/edit

---

## 5. Customer Management Screen

### Purpose
Manage customer database and loyalty programs.

### Layout Structure

```
┌─────────────────────────────────────────────────────────────┐
│ CUSTOMER MANAGEMENT                                         │
├─────────────────────────────────────────────────────────────┤
│ [+ Add Customer] [Import] [Export]                          │
│ Search: [__________________]  Type: [All ▼]  Active: [All ▼]│
│                                                              │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ CUSTOMERS TABLE                                         │ │
│ │ ┌─────┬──────────┬────────┬──────────┬──────┬─────────┐ │
│ │ │ID   │Name      │Phone   │Email     │Type  │ Points  │ │
│ │ ├─────┼──────────┼────────┼──────────┼──────┼─────────┤ │
│ │ │C001 │John Doe  │555-1234│john@...  │VIP   │ 1,250   │ │
│ │ │C002 │Jane Smith│555-5678│jane@...  │Reg   │ 345     │ │
│ │ │C003 │Guest     │-       │-         │Reg   │ 0       │ │
│ │ │     │          │        │          │      │         │ │
│ │ │     │[View] [Edit] [Delete]                          │ │
│ │ └─────┴──────────┴────────┴──────────┴──────┴─────────┘ │
│ │ Showing 1-10 of 523 | [< Page 1 of 53 >]                │ │
│ └─────────────────────────────────────────────────────────┘ │
│                                                              │
│ LOYALTY PROGRAM SETTINGS                                    │
│ Points per $1 spent: 1 | Redemption rate: 1 point = $0.01  │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### Customer Detail View

```
┌──────────────────────────────────────────────┐
│ CUSTOMER PROFILE: John Doe             [Edit]│
├──────────────────────────────────────────────┤
│ CONTACT INFORMATION                         │
│ Phone: 555-1234                             │
│ Email: john@example.com                     │
│ Address: 123 Main St, City, State 12345     │
│                                              │
│ CUSTOMER DETAILS                            │
│ Type: VIP  |  Active: Yes                   │
│ Loyalty Points: 1,250                       │
│ Total Purchases: $5,250.00                  │
│                                              │
│ PURCHASE HISTORY                            │
│ ┌────────┬────────┬─────────────┐          │
│ │ Date   │ Amount │ Items       │          │
│ ├────────┼────────┼─────────────┤          │
│ │ 15 Jan │ $234.50│ 5 items     │          │
│ │ 10 Jan │ $456.75│ 8 items     │          │
│ │ 05 Jan │ $123.45│ 3 items     │          │
│ └────────┴────────┴─────────────┘          │
│                                              │
│ [Close]  [Send Email]  [Call]               │
│                                              │
└──────────────────────────────────────────────┘
```

### Components Used
- Searchable customer table
- Customer profile drawer
- Loyalty points display
- Purchase history table
- Email/SMS communication tools

---

## 6. Reports Dashboard

### Purpose
Provide business intelligence and actionable insights.

### Layout Structure

```
┌─────────────────────────────────────────────────────────────┐
│ REPORTS & ANALYTICS                                         │
├─────────────────────────────────────────────────────────────┤
│ Report Type: [All Reports ▼]                                │
│ Date Range: [From] [To]  [Apply]  [Today] [This Week] ...  │
│ [Export PDF] [Email Report]                                 │
│                                                              │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ Sales Performance (Last 30 Days)                        │ │
│ │                                                         │ │
│ │ Total Sales: $45,230.50 ▲ 12% vs last month           │ │
│ │ Transactions: 1,234 ▼ 5% vs last month                │ │
│ │ Avg Transaction: $36.70 ▲ 18% vs last month           │ │
│ │                                                         │ │
│ │ [Chart: Sales Trend - Line Chart]                      │ │
│ │                                                         │ │
│ │ Top 10 Products by Revenue                             │ │
│ │ ┌─────┬──────────┬──────────┬─────────────┐           │ │
│ │ │Rank │ Product  │ Revenue  │ Units Sold  │           │ │
│ │ ├─────┼──────────┼──────────┼─────────────┤           │ │
│ │ │  1  │Product A │ $5,234.50│   234 units │           │ │
│ │ │  2  │Product B │ $4,123.75│   167 units │           │ │
│ │ │  3  │Product C │ $3,456.25│   289 units │           │ │
│ │ └─────┴──────────┴──────────┴─────────────┘           │ │
│ │                                                         │ │
│ │ [Chart: Sales by Category - Pie Chart]                 │ │
│ │                                                         │ │
│ │ [Chart: Sales by Payment Method - Bar Chart]            │ │
│ │                                                         │ │
│ └─────────────────────────────────────────────────────────┘ │
│                                                              │
│ FINANCIAL SUMMARY                                           │
│ Gross Revenue: $45,230.50                                   │
│ Discounts Given: -$1,234.50                                 │
│ Taxes Collected: $3,456.78                                  │
│ Net Revenue: $47,452.78                                     │
│                                                              │
│ OPERATIONAL INSIGHTS                                        │
│ Busiest Hour: 12:00 PM - 1:00 PM (234 transactions)         │
│ Best Selling Category: Electronics (45% of revenue)         │
│ Customer Return Rate: 0.8%                                  │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### Components Used
- Chart.js visualizations
- KPI cards with trend indicators
- Comparative metrics
- Filter by date range
- Export to PDF/Excel
- Data drill-down capabilities

---

## 7. Settings & Configuration Screen

### Purpose
System-wide settings, user management, and business configuration.

### Layout Structure

```
┌─────────────────────────────────────────────────────────────┐
│ SETTINGS & CONFIGURATION                                    │
├─────────────────────────────────────────────────────────────┤
│ Settings Category:                                          │
│ [System] [Business] [Localization] [Security] [Users]      │
│ [API] [Integrations] [Notifications]                        │
│                                                              │
│ BUSINESS SETTINGS                                           │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ Business Name: [________________]                       │ │
│ │ Business Phone: [________________]                      │ │
│ │ Business Email: [________________]                      │ │
│ │ Business Address: [_____________________]              │ │
│ │                                                         │ │
│ │ TAX CONFIGURATION                                       │ │
│ │ Default Tax Rate: [_____]%                             │ │
│ │ Tax Calculation: [On Sale Amount ▼]                    │ │
│ │ Display Prices: [With Tax ▼]                           │ │
│ │                                                         │ │
│ │ CURRENCY SETTINGS                                       │ │
│ │ Currency: [USD ▼]                                       │ │
│ │ Currency Symbol: [$]                                    │ │
│ │ Decimal Places: [2]                                     │ │
│ │                                                         │ │
│ │ RECEIPT SETTINGS                                        │ │
│ │ Company Name on Receipt: [__________________]           │ │
│ │ Show QR Code: [☑] Show Loyalty Points: [☑]             │ │
│ │ Receipt Footer Text: [____________________]             │ │
│ │                                                         │ │
│ │ [Save Changes]                                          │ │
│ │                                                         │ │
│ └─────────────────────────────────────────────────────────┘ │
│                                                              │
│ USER MANAGEMENT                                             │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ [+ Add New User]                                        │ │
│ │ ┌────┬──────────┬────────┬──────────┬──────────────┐   │ │
│ │ │ID  │Name      │Email   │Role      │Last Login    │   │ │
│ │ ├────┼──────────┼────────┼──────────┼──────────────┤   │ │
│ │ │U01 │John Doe  │john... │Admin     │17 Jan 10:30  │   │ │
│ │ │    │[Edit] [Disable]                           │   │ │
│ │ │U02 │Jane Smith│jane... │Manager   │16 Jan 14:22  │   │ │
│ │ │    │[Edit] [Disable]                           │   │ │
│ │ └────┴──────────┴────────┴──────────┴──────────────┘   │ │
│ │                                                         │ │
│ └─────────────────────────────────────────────────────────┘ │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### Components Used
- Tabbed interface for settings categories
- Text input fields with validation
- Dropdown selectors
- Toggle switches
- User management table
- Save confirmation dialogs

---

## 8. AI Chatbot Interface

### Purpose
Customer service and operational intelligence via conversational AI.

### Layout Structure

```
┌──────────────────────────────────────────────┐
│ POS ASSISTANT CHATBOT               [_] [X]  │
├──────────────────────────────────────────────┤
│ CONVERSATION AREA                           │
│ ┌────────────────────────────────────────┐  │
│ │ Hello! How can I help you today?       │  │ (System)
│ │                                        │  │
│ │ I'd like to check product availability│  │ (User)
│ │                                        │  │
│ │ Sure! Which product are you looking   │  │ (System)
│ │ for?                                   │  │
│ │                                        │  │
│ │ Product XYZ                            │  │ (User)
│ │                                        │  │
│ │ Great! Product XYZ is currently in    │  │ (System)
│ │ stock. We have 45 units available at  │  │
│ │ $24.99 each. Would you like me to:   │  │
│ │ [Add to Cart] [More Details] [Similar]│  │
│ │                                        │  │
│ │ [Scroll for more...]                  │  │
│ └────────────────────────────────────────┘  │
│                                              │
│ MESSAGE INPUT:                              │
│ ┌────────────────────────────────────────┐  │
│ │ [Type your message...              ]   │  │
│ │ [Send]  [Quick Options ▼]              │  │
│ └────────────────────────────────────────┘  │
│                                              │
│ Quick Options:                              │
│ [Check Stock] [Product Info] [Track Order]  │
│ [Return Info] [Contact Support] [Offers]    │
│                                              │
│ [Clear Chat] [Export]                       │
│                                              │
└──────────────────────────────────────────────┘
```

### Features
- Conversational AI responses
- Quick action buttons
- Context-aware suggestions
- Integration with product database
- Multi-language support (future)
- User feedback collection
- Session history

### Interactive Elements
- Text input with autocomplete
- Quick action buttons
- Suggestion chips
- Export conversation
- Chat history sidebar

---

## 9. Mobile Responsive Design

### Breakpoints
```css
/* Mobile First Approach */
Extra Small (XS): < 576px
Small (SM): ≥ 576px
Medium (MD): ≥ 768px
Large (LG): ≥ 992px
Extra Large (XL): ≥ 1200px
XXL: ≥ 1400px
```

### Mobile POS Screen (Mobile-First Design)
```
┌──────────────────────────┐
│ [≡] POS [Notifications] │
├──────────────────────────┤
│ QUICK SEARCH             │
│ [Search/Barcode]         │
│                          │
│ CART (3 items)  $340.98  │
│ [View Cart]              │
│                          │
│ CATEGORY QUICK ACCESS    │
│ [Electronics][Groceries] │
│ [Clothing][Other]        │
│                          │
│ TOP PRODUCTS             │
│ ┌──────────┐ ┌────────┐ │
│ │[Image]   │ │[Image] │ │
│ │Product A │ │Prod B  │ │
│ │$24.99    │ │$45.50  │ │
│ │[Add]     │ │[Add]   │ │
│ └──────────┘ └────────┘ │
│                          │
│ ┌──────────┐             │
│ │[Image]   │             │
│ │Product C │             │
│ │$8.99     │             │
│ │[Add]     │             │
│ └──────────┘             │
│                          │
│ [CHECKOUT] [SUSPEND]     │
│                          │
└──────────────────────────┘
```

---

## 10. Accessibility Standards (WCAG 2.1 AA)

### Requirements
1. **Color Contrast**: Minimum 4.5:1 for normal text
2. **Keyboard Navigation**: All features accessible via keyboard
3. **ARIA Labels**: Proper semantic HTML and ARIA labels
4. **Focus Indicators**: Visible focus states on all interactive elements
5. **Alt Text**: Descriptive alt text for all images
6. **Form Labels**: Associated labels for all form inputs
7. **Error Messages**: Clear, specific error messages
8. **Page Structure**: Proper heading hierarchy
9. **Responsive Text**: Readable at 200% zoom
10. **Motion**: No auto-playing animations

### Implementation
- ARIA roles and labels on custom components
- Focus management for modals and drawers
- Skip-to-content links
- Screen reader testing (NVDA, JAWS)
- Keyboard-only navigation testing
- Color blind friendly palettes

---

## 11. Design System Components

### Standard Components
```
Buttons:
├── Primary (CTA)
├── Secondary
├── Danger (Delete/Remove)
├── Success (Confirm)
├── Small / Medium / Large sizes
└── Loading states

Input Fields:
├── Text input
├── Number input
├── Email input
├── Date/Time picker
├── Dropdown select
├── Checkbox / Radio
└── File upload

Feedback:
├── Success toast
├── Error toast
├── Warning alert
├── Info alert
├── Loading spinner
└── Skeleton loader

Modals:
├── Confirmation dialog
├── Form modal
├── Alert modal
└── Custom modal

Tables:
├── Sortable columns
├── Pagination
├── Row selection
├── Inline actions
└── Export options
```

---

## 12. Performance & Loading States

### Loading Indicators
- Skeleton loaders for page content
- Progress spinners for operations
- Skeleton cards for product lists
- Progress bars for uploads

### Empty States
```
┌─────────────────────────────┐
│ No products found           │
│ [Icon]                      │
│ "Your search didn't match   │
│  any products"              │
│ [Try different keywords]    │
│                             │
│ [Browse All] [Go Home]      │
└─────────────────────────────┘
```

### Error States
```
┌─────────────────────────────┐
│ ⚠ Something went wrong       │
│ The page failed to load.     │
│ Error: [Error code]         │
│                             │
│ [Retry] [Go Home]           │
└─────────────────────────────┘
```

---

## 13. Print Layouts

### Receipt Template
```
═══════════════════════════════
       BUSINESS NAME
       123 Main Street
       Phone: 555-1234
═══════════════════════════════

Sale #: SO-20240117-001
Date: 17 Jan 2024, 10:32 AM
Cashier: John

───────────────────────────────
Item           Qty    Price
───────────────────────────────
Product A      2      $24.99
Product B      1      $45.50
Product C      3      $15.00

Subtotal:              $304.99
Tax (8%):               $24.40
Discount (3%):         -$10.00
───────────────────────────────
TOTAL:                $319.39

Payment: Cash
Change Due: $80.61
───────────────────────────────

Thank You for Your Business!
[QR Code]
═══════════════════════════════
```

---

## Design Implementation Guide

### File Structure
```
public/
├── css/
│   ├── bootstrap.min.css
│   ├── main.css           (Global styles)
│   ├── components.css     (Reusable components)
│   ├── responsive.css     (Media queries)
│   └── print.css          (Print styles)
├── js/
│   ├── bootstrap.min.js
│   ├── chart.js
│   ├── main.js            (App initialization)
│   ├── components.js      (UI components)
│   ├── api.js             (API calls)
│   └── utils.js           (Utility functions)
└── images/
    ├── logo.png
    ├── icons/
    └── backgrounds/
```

### CSS Naming Convention (BEM)
```css
.button { }                  /* Block */
.button--primary { }       /* Modifier */
.button__text { }          /* Element */
.button__text--bold { }    /* Element modifier */

.card { }
.card__header { }
.card__body { }
.card__footer { }
```

---

## Testing Checklist

- [ ] All pages responsive on mobile/tablet/desktop
- [ ] Color contrast ratios meet WCAG AA
- [ ] Keyboard navigation works on all interactive elements
- [ ] Forms have proper labels and error messages
- [ ] Images have alt text
- [ ] Page load time < 2 seconds
- [ ] All icons have proper sizing
- [ ] Print layouts are correct
- [ ] PDF export works correctly
- [ ] Touch targets are minimum 44x44px
- [ ] Loading states are visible
- [ ] Error states are clear
- [ ] Modals are properly accessible
- [ ] Links are distinguishable from regular text

---

This wireframe specification provides the foundation for developing a professional, user-friendly POS system interface that balances efficiency with accessibility.
