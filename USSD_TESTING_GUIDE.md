# USSD Testing Guide

## 🚀 Getting Started

### 1. Access the USSD Simulator
Open your browser and navigate to:
```
http://localhost/raffle/public/ussd-simulator.php
```

### 2. Test Phone Number
Default test number: `233241234567`
You can change this in the phone input field.

---

## 📱 USSD Flow Testing

### Main Menu
When you first load the simulator, you'll see:
```
CON Welcome to Raffle System
1. Buy Ticket
2. Check My Tickets
3. Check Winners
4. My Balance
0. Exit
```

---

## 🎯 Test Scenarios

### Scenario 1: Buy a Ticket

**Step 1:** Press `1` (Buy Ticket)
- You'll see a list of active stations

**Step 2:** Select a station (e.g., press `1`)
- You'll see programmes for that station

**Step 3:** Select a programme (e.g., press `1`)
- You'll see active campaigns

**Step 4:** Select a campaign (e.g., press `1`)
- You'll see ticket quantity options:
  - 1 = 1 ticket
  - 2 = 2 tickets
  - 3 = 3 tickets
  - 4 = 5 tickets
  - 5 = Custom amount

**Step 5:** Select quantity (e.g., press `1`)
- You'll see payment confirmation with total amount

**Step 6:** Press `1` to confirm
- You'll see payment method options:
  - 1 = MTN Mobile Money
  - 2 = Vodafone Cash
  - 3 = AirtelTigo Money

**Step 7:** Select payment method (e.g., press `1`)
- You'll receive a payment reference and instructions

**Expected Result:**
```
END Payment request sent to your phone.
Amount: GHS 5.00
Please approve the prompt to complete purchase.
Reference: PAY123
```

**Step 8:** Complete the payment
- A green "Complete Payment" button will appear below the simulator
- Click "✅ Complete Payment & Generate Tickets"
- This opens the manual payment page in a new tab
- The payment will be processed and tickets generated automatically
- Check the database to verify tickets were created

**Expected Result:**
- Payment status changes to 'success'
- Tickets are generated with unique codes
- Revenue is allocated to platform, station, programme, and prize pool
- Station wallet is credited with commission

---

### Scenario 2: Check My Tickets

**Step 1:** From main menu, press `2`

**Expected Result:**
```
END Your Recent Tickets:

Code: TKT-ABC123
Campaign: Christmas Raffle
Date: 01 Dec 2024

(or "You have no tickets yet." if no tickets)
```

---

### Scenario 3: Check Winners

**Step 1:** From main menu, press `3`

**Expected Result:**
```
END Congratulations! You Won:

Campaign: Christmas Raffle 2024
Prize: GHS 500.00
Rank: 1
Status: PAID
Date: 10 Dec 2024

(or "You haven't won yet. Keep playing!" if no wins)
```

---

### Scenario 4: Check Balance

**Step 1:** From main menu, press `4`

**Expected Result:**
```
END Account Balance:

Name: Player Name
Phone: 233241234567
Total Tickets: 5
Total Winnings: GHS 500.00
Loyalty Points: 50
```

---

### Scenario 5: Exit

**Step 1:** From main menu, press `0`

**Expected Result:**
```
END Thank you for using Raffle System!
```

---

## 🔄 Navigation Tips

### Going Back
- Press `0` at most menus to go back to the previous screen
- The simulator tracks your session state

### Reset Session
- Click the "🔄 Reset" button to start a new session
- This clears all session data and starts fresh

### Keyboard Support
- You can use your keyboard to type numbers (0-9)
- Press *, # keys directly

---

## 🐛 Troubleshooting

### Issue: "ERROR: HTTP 404"
**Solution:** Make sure the USSD controller route is properly configured in your routing system.

### Issue: No stations/programmes/campaigns showing
**Solution:** 
1. Check that you have active stations in the database
2. Verify programmes are linked to stations
3. Ensure campaigns are active and have programme access

### Issue: Session not maintaining state
**Solution:** 
1. Check that PHP sessions are enabled
2. Verify the `ussd_sessions` table exists in the database
3. Check PHP error logs for session-related errors

---

## 📊 Database Verification

### Check if tables exist:
```sql
SHOW TABLES LIKE 'ussd_sessions';
SHOW TABLES LIKE 'sms_logs';
SHOW TABLES LIKE 'api_keys';
SHOW TABLES LIKE 'otp_verifications';
```

### View USSD sessions:
```sql
SELECT * FROM ussd_sessions ORDER BY created_at DESC LIMIT 10;
```

### View SMS logs:
```sql
SELECT * FROM sms_logs ORDER BY created_at DESC LIMIT 10;
```

---

## 🎨 Simulator Features

### Visual Feedback
- **Active Session:** Green badge
- **Ended Session:** Red badge
- **Response Display:** Shows CON (continue) or END (terminate) messages

### Session Information Panel
Shows:
- Session ID
- Phone Number
- Current Input Text

### Phone Keypad
- Realistic phone interface
- Click buttons or use keyboard
- Visual feedback on button press

---

## 🔐 Security Notes

⚠️ **Important:** This simulator is for DEVELOPMENT and TESTING only!

- Do not use in production
- Contains hardcoded test data
- No authentication required
- Sessions stored in PHP sessions (not persistent)

---

## 📝 Next Steps

After testing USSD:
1. Test the Mobile API endpoints
2. Configure SMS gateway credentials
3. Set up payment gateway integration
4. Test end-to-end ticket purchase flow

---

## 💡 Tips for Better Testing

1. **Test with Multiple Stations:** Create multiple stations/programmes/campaigns
2. **Test Edge Cases:** Try invalid inputs, going back, canceling
3. **Test Session Timeout:** Leave session idle and try to continue
4. **Test Different Phone Numbers:** Change the phone number to test different users
5. **Monitor Database:** Watch the `ussd_sessions` table to see session data

---

## 🆘 Support

If you encounter issues:
1. Check PHP error logs: `C:\xampp\php\logs\php_error_log`
2. Check Apache error logs: `C:\xampp\apache\logs\error.log`
3. Enable PHP error display in `php.ini`
4. Check database connection in `app/config/config.php`

---

Happy Testing! 🎉
