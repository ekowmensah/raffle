# Remaining Features Implementation Guide

## ✅ 1. Password Reset - IMPLEMENTED

**Status:** Complete
**Files Created:**
- `app/models/PasswordReset.php`
- `app/views/auth/forgot-password.php`
- `app/views/auth/reset-password.php`
- Updated `AuthController.php` with forgotPassword() and resetPassword()

**Database Migration Needed:**
```sql
CREATE TABLE password_resets (
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_token (token)
) ENGINE=InnoDB;
```

---

## 2. Payment Reconciliation View - TO IMPLEMENT

**Purpose:** Allow admins to reconcile payments and identify discrepancies

**Implementation:**
1. Add method to PaymentController:
```php
public function reconcile() {
    $this->requireAuth();
    $this->requireRole('super_admin', 'finance');
    
    $startDate = $_GET['start_date'] ?? date('Y-m-01');
    $endDate = $_GET['end_date'] ?? date('Y-m-d');
    
    $paymentModel = $this->model('Payment');
    $payments = $paymentModel->getForReconciliation($startDate, $endDate);
    
    // Calculate totals
    $totalSuccess = 0;
    $totalPending = 0;
    $totalFailed = 0;
    
    foreach ($payments as $payment) {
        if ($payment->status == 'success') $totalSuccess += $payment->amount;
        elseif ($payment->status == 'pending') $totalPending += $payment->amount;
        else $totalFailed += $payment->amount;
    }
    
    $data = [
        'title' => 'Payment Reconciliation',
        'payments' => $payments,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'total_success' => $totalSuccess,
        'total_pending' => $totalPending,
        'total_failed' => $totalFailed
    ];
    
    $this->view('payments/reconcile', $data);
}
```

2. Add to Payment model:
```php
public function getForReconciliation($startDate, $endDate) {
    $this->db->query("SELECT p.*, 
                     c.name as campaign_name,
                     pl.phone as player_phone,
                     s.name as station_name
                     FROM {$this->table} p
                     LEFT JOIN raffle_campaigns c ON p.campaign_id = c.id
                     LEFT JOIN players pl ON p.player_id = pl.id
                     LEFT JOIN stations s ON p.station_id = s.id
                     WHERE DATE(p.created_at) BETWEEN :start_date AND :end_date
                     ORDER BY p.created_at DESC");
    $this->db->bind(':start_date', $startDate);
    $this->db->bind(':end_date', $endDate);
    return $this->db->resultSet();
}
```

3. Create view `app/views/payments/reconcile.php` with date filters and export to CSV option

---

## 3. Revenue Allocation Reports - TO IMPLEMENT

**Purpose:** Show revenue breakdown by campaign, station, programme

**Implementation:**
1. Create ReportController:
```php
class ReportController extends Controller {
    public function revenue() {
        $this->requireAuth();
        
        $campaignId = $_GET['campaign'] ?? null;
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        $revenueModel = $this->model('RevenueAllocation');
        $report = $revenueModel->getRevenueReport($campaignId, $startDate, $endDate);
        
        $data = [
            'title' => 'Revenue Report',
            'report' => $report,
            'campaigns' => $this->model('Campaign')->findAll()
        ];
        
        $this->view('reports/revenue', $data);
    }
}
```

2. Add to RevenueAllocation model:
```php
public function getRevenueReport($campaignId = null, $startDate, $endDate) {
    $sql = "SELECT 
            c.name as campaign_name,
            s.name as station_name,
            p.name as programme_name,
            SUM(ra.platform_commission_amount) as platform_total,
            SUM(ra.station_commission_amount) as station_total,
            SUM(ra.programme_commission_amount) as programme_total,
            SUM(ra.winner_pool_amount_total) as prize_pool_total,
            COUNT(DISTINCT ra.payment_id) as payment_count
            FROM {$this->table} ra
            LEFT JOIN raffle_campaigns c ON ra.campaign_id = c.id
            LEFT JOIN stations s ON ra.station_id = s.id
            LEFT JOIN programmes p ON ra.programme_id = p.id
            WHERE DATE(ra.created_at) BETWEEN :start_date AND :end_date";
    
    if ($campaignId) {
        $sql .= " AND ra.campaign_id = :campaign_id";
    }
    
    $sql .= " GROUP BY ra.campaign_id, ra.station_id, ra.programme_id";
    
    $this->db->query($sql);
    $this->db->bind(':start_date', $startDate);
    $this->db->bind(':end_date', $endDate);
    if ($campaignId) $this->db->bind(':campaign_id', $campaignId);
    
    return $this->db->resultSet();
}
```

---

## 4. Promo Code Analytics - TO IMPLEMENT

**Already exists in PromoCodeController** - just needs view creation!

Create `app/views/promo-codes/analytics.php` showing:
- Usage count
- Total revenue generated
- Commission earned
- Top users/stations using the code

---

## 5. Draw Analytics Dashboard - TO IMPLEMENT

**Purpose:** Show draw statistics and trends

**Implementation:**
1. Add to DrawController:
```php
public function analytics() {
    $this->requireAuth();
    
    $campaignId = $_GET['campaign'] ?? null;
    
    $drawModel = $this->model('Draw');
    $winnerModel = $this->model('DrawWinner');
    
    $stats = [
        'total_draws' => $drawModel->countByCampaign($campaignId),
        'total_winners' => $winnerModel->countByCampaign($campaignId),
        'total_prizes' => $winnerModel->getTotalPrizesAwarded($campaignId),
        'avg_prize' => 0,
        'draws_by_type' => $drawModel->countByType($campaignId)
    ];
    
    $data = [
        'title' => 'Draw Analytics',
        'stats' => $stats,
        'campaigns' => $this->model('Campaign')->findAll()
    ];
    
    $this->view('draws/analytics', $data);
}
```

---

## 6. Ticket Sales Charts - TO IMPLEMENT

**Requires:** Chart.js library

**Implementation:**
1. Add Chart.js to layout:
```html
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
```

2. Create charts in campaign dashboard for:
- Daily ticket sales
- Revenue trends
- Tickets by station
- Tickets by programme

---

## 7. Advanced Player Search/Filtering - TO IMPLEMENT

**Enhancement to existing PlayerController**

Add filters for:
- Loyalty level
- Total spent range
- Registration date range
- Number of tickets purchased
- Campaign participation

---

## 8. Minimum Turnover Validation - TO IMPLEMENT

Add to DrawService before conducting draw:
```php
$campaign = $this->campaignModel->findById($draw->campaign_id);
if ($campaign->minimum_turnover_required) {
    $totalRevenue = $this->paymentModel->getTotalRevenue($campaign->id);
    if ($totalRevenue < $campaign->minimum_turnover_amount) {
        return ['success' => false, 'message' => 'Minimum turnover not met'];
    }
}
```

---

## 9. Weighted Winner Selection - TO IMPLEMENT

Modify DrawService::selectWinners() to weight by ticket count:
```php
// Build weighted array
$weightedTickets = [];
foreach ($eligibleTickets as $ticket) {
    $playerTicketCount = count(array_filter($eligibleTickets, fn($t) => $t->player_id == $ticket->player_id));
    for ($i = 0; $i < $playerTicketCount; $i++) {
        $weightedTickets[] = $ticket;
    }
}
// Then select from weighted array
```

---

## 10. Multiple Prize Types - TO IMPLEMENT

**Database already supports:** cash, airtime, data, voucher, other

Add UI in draw scheduling to specify prize type and handle distribution accordingly.

---

## 11. Winner Verification Workflow - TO IMPLEMENT

Add status field to draw_winners: unverified → verified → paid

Create verification interface for admins to confirm winners before payment.

---

## 12. Winner Announcement Publishing - TO IMPLEMENT

Add "Publish Winners" button that:
1. Updates draw status to 'published'
2. Makes winners visible on public page
3. Sends announcement SMS/email

---

## 13. Upcoming Draws Calendar - TO IMPLEMENT

Create calendar view showing all scheduled draws with color coding by type.

**Requires:** FullCalendar.js library

---

## 14. Audit Logs System - TO IMPLEMENT

**Database table already exists!**

Create AuditLog model and middleware to log:
- User actions
- Campaign changes
- Payment transactions
- Draw executions
- Winner selections

---

## Priority Implementation Order

### High Priority (Critical for Production):
1. ✅ Password Reset - DONE
2. Payment Reconciliation View
3. Revenue Allocation Reports
4. Draw Analytics Dashboard

### Medium Priority (Business Value):
5. Promo Code Analytics
6. Ticket Sales Charts
7. Minimum Turnover Validation
8. Advanced Player Filtering

### Low Priority (Nice to Have):
9. Weighted Winner Selection
10. Multiple Prize Types
11. Winner Verification Workflow
12. Winner Announcement Publishing
13. Upcoming Draws Calendar
14. Audit Logs System

---

## Estimated Implementation Time

- Payment Reconciliation: 2 hours
- Revenue Reports: 3 hours
- Promo Analytics: 1 hour
- Draw Analytics: 2 hours
- Charts Integration: 4 hours
- Advanced Filtering: 2 hours
- Other Features: 10-15 hours

**Total: ~25-30 hours for all features**

---

## Next Steps

1. Create password_resets table
2. Test password reset functionality
3. Implement payment reconciliation (highest business priority)
4. Add revenue reports
5. Integrate Chart.js for visualizations
6. Implement remaining features based on business needs

