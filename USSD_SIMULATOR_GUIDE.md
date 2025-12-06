# USSD Simulator - Enhanced Version

## Access
**URL**: `http://localhost/raffle/public/ussd-simulator.php`

## New Features

### 1. **3-Panel Layout**
- **Left Panel**: Debug Information & Quick Actions
- **Center Panel**: Phone Simulator
- **Right Panel**: Session History

### 2. **Debug Panel** 🔍
- HTTP Response Code
- Response Time (ms)
- Timestamp
- Session Statistics (Steps, Duration)
- Quick Action Buttons

### 3. **Enhanced Phone Simulator** 📱
- Response Type Indicator (CON/END)
- Back Button (⬅️) - Go back one step
- Reset Button (🔄) - Start fresh session
- Visual status badges
- Keyboard support (0-9, *, #)

### 4. **Session History** 📜
- Complete step-by-step history
- Input and response for each step
- Timestamps
- Full input text path
- Reverse chronological order (newest first)

### 5. **Performance Tracking**
- Response time monitoring
- Session duration tracking
- Step counting
- HTTP status codes

## How to Use

### Basic Flow
1. **Start**: Simulator loads with welcome screen
2. **Navigate**: Click number keys or use keyboard
3. **Go Back**: Click ⬅️ Back button to undo last step
4. **Reset**: Click 🔄 Reset to start over
5. **Debug**: Monitor response times and HTTP codes in left panel
6. **History**: Review all steps in right panel

### Quick Actions
- **Buy Ticket**: Instantly sends "1"
- **My Tickets**: Instantly sends "2"
- **Winners**: Instantly sends "3"
- **Back/Exit**: Instantly sends "0"

### Keyboard Shortcuts
- Press `0-9` on keyboard to send numbers
- Press `*` or `#` for special characters
- No need to click buttons!

## Features Explained

### Back Button
- Removes last input from session
- Re-requests previous menu
- Disabled when no input exists
- Useful for testing navigation

### Debug Information
```
HTTP Code: 200          ← Server response status
Response Time: 45ms     ← How fast the server responded
Timestamp: 2025-12-06   ← When request was made
Steps: 5                ← Number of inputs made
Duration: 32s           ← Total session time
```

### History Display
```
Step 5: Input "2"
Response: CON Select Campaign:
1. Christmas Promo (GHS 5.00)
2. New Year Raffle (GHS 10.00)
0. Back

14:23:45 (1*1*2)       ← Time and full input path
```

## Testing Scenarios

### Test 1: Complete Ticket Purchase
1. Press `1` (Buy Ticket)
2. Press `1` (Select Station)
3. Press `1` (Select Campaign)
4. Press `2` (Quantity)
5. Press `1` (Confirm)
6. Check payment ID in history panel
7. Click "Complete Payment" button

### Test 2: Navigation Testing
1. Press `1` (Buy Ticket)
2. Press `1` (Select Station)
3. Click ⬅️ Back
4. Verify you're back at station selection
5. Press `2` (Different station)

### Test 3: Performance Testing
1. Monitor response times in debug panel
2. Check if any requests take >500ms
3. Review HTTP codes for errors
4. Track session duration

### Test 4: Error Handling
1. Enter invalid phone number
2. Try to go back at start (button disabled)
3. Check error messages in display

## Improvements Over Old Version

| Feature | Old | New |
|---------|-----|-----|
| Layout | Single column | 3-panel responsive grid |
| Debug Info | None | Full debug panel |
| History | None | Complete session history |
| Back Button | ❌ | ✅ |
| Response Time | ❌ | ✅ |
| Quick Actions | ❌ | ✅ |
| Step Counter | ❌ | ✅ |
| Response Type | ❌ | ✅ CON/END indicator |
| Session Duration | ❌ | ✅ |
| Keyboard Support | Limited | Full support |

## Tips for Testing

### 1. Monitor Performance
- Watch response times
- Identify slow endpoints
- Check for timeouts

### 2. Use History
- Review complete flow
- Identify where users might get stuck
- Verify correct menu sequences

### 3. Test Edge Cases
- Go back at each step
- Try invalid inputs
- Test session timeout
- Check payment flow

### 4. Quick Testing
- Use Quick Action buttons
- Skip repetitive navigation
- Jump to specific menus faster

## Troubleshooting

### Issue: Blank Screen
- Check if USSD controller exists
- Verify URL: `/index.php?url=ussd`
- Check PHP error logs

### Issue: Slow Response
- Check debug panel for response times
- Review database queries
- Check server load

### Issue: Back Button Not Working
- Verify session is active
- Check if text path exists
- Review browser console for errors

### Issue: History Not Showing
- Ensure session is initialized
- Check if history array exists
- Verify PHP session is working

## Browser Compatibility
- ✅ Chrome/Edge (Recommended)
- ✅ Firefox
- ✅ Safari
- ✅ Mobile browsers

## Responsive Design
- Desktop: 3-column layout
- Tablet: Stacked layout
- Mobile: Single column (phone first)

## Future Enhancements
- [ ] Export session history
- [ ] Save/load test scenarios
- [ ] Automated testing scripts
- [ ] Network throttling simulation
- [ ] Multiple concurrent sessions
- [ ] Session replay feature
