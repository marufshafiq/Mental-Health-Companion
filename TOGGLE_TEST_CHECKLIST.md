# 🧪 Sidebar Toggle Feature - Test Checklist

**Test Date:** October 11, 2025  
**Feature:** ChatGPT-style Collapsible Sidebar  
**Server:** http://localhost:8000

---

## ✅ Test Cases

### **1. Dashboard Page (dashboard.php)**

#### Visual Tests:
- [ ] Sidebar is visible on the left (250px width) by default
- [ ] Logo (🧠) and "Mental Health Companion" title are visible
- [ ] All navigation items show both icons and text
- [ ] Active item (Dashboard) is highlighted

#### Toggle Functionality:
- [ ] **Click the 🧠 brain logo** - Sidebar collapses to 80px
- [ ] When collapsed, only icons are visible
- [ ] Navigation text disappears smoothly with opacity transition
- [ ] Main content shifts left (margin-left changes to 80px)
- [ ] **Click logo again** - Sidebar expands back to 250px
- [ ] Text reappears smoothly

#### Tooltip Tests (Collapsed State):
- [ ] Hover over Dashboard icon (📊) - Tooltip shows "Dashboard"
- [ ] Hover over Journal icon (📓) - Tooltip shows "Journal"
- [ ] Hover over Mood Tracker icon (😊) - Tooltip shows "Mood Tracker"
- [ ] Hover over AI Chatbot icon (💬) - Tooltip shows "AI Chatbot"
- [ ] Hover over Meditation icon (🧘‍♀️) - Tooltip shows "Meditation & Resources"
- [ ] Hover over Profile icon (👤) - Tooltip shows "Profile"
- [ ] Hover over Logout icon (🚪) - Tooltip shows "Logout"

#### Animation Tests:
- [ ] Width transition is smooth (0.3s ease)
- [ ] Text fade-out is smooth
- [ ] Main content margin transition is smooth
- [ ] No visual glitches or jumps

---

### **2. Chat Page (views/chat.php)**

#### Navigation to Chat:
- [ ] Click "AI Chatbot" link from dashboard
- [ ] Page loads correctly

#### Visual Tests:
- [ ] Sidebar structure matches dashboard
- [ ] Chat page shows "AI Chatbot" as active
- [ ] Logo and navigation items are properly displayed

#### Toggle Functionality:
- [ ] **Click the 🧠 brain logo** - Sidebar collapses
- [ ] Chat container adjusts properly
- [ ] **Click logo again** - Sidebar expands
- [ ] No layout breaking or overlapping

#### Tooltip Tests:
- [ ] All tooltips work when sidebar is collapsed
- [ ] Tooltips appear on hover
- [ ] Tooltip text is readable

---

### **3. Meditation Page (views/meditation.php)**

#### Navigation to Meditation:
- [ ] Click "Meditation & Resources" link
- [ ] Page loads correctly

#### Visual Tests:
- [ ] ✅ **"Back to Dashboard" button is REMOVED**
- [ ] ✅ **Sidebar is present instead**
- [ ] "Meditation & Resources" is highlighted as active
- [ ] Purple gradient "Daily Wellness Tip" displays correctly
- [ ] Resources section is properly formatted

#### Toggle Functionality:
- [ ] **Click the 🧠 brain logo** - Sidebar collapses
- [ ] Tip card and resources adjust properly
- [ ] Main content doesn't overlap with collapsed sidebar
- [ ] **Click logo again** - Sidebar expands

#### Styling Tests:
- [ ] Purple gradient tip card looks therapeutic
- [ ] Tip card animation (gentle glow) works
- [ ] Resources cards display properly
- [ ] No broken layouts or misaligned content

---

### **4. LocalStorage Persistence Tests**

#### Cross-Page State Persistence:
1. **Test Scenario 1:**
   - [ ] Go to dashboard.php
   - [ ] Collapse sidebar (click logo)
   - [ ] Navigate to chat.php
   - [ ] ✅ Sidebar should REMAIN COLLAPSED
   - [ ] Navigate to meditation.php
   - [ ] ✅ Sidebar should STILL BE COLLAPSED

2. **Test Scenario 2:**
   - [ ] While on meditation.php (collapsed)
   - [ ] Expand sidebar (click logo)
   - [ ] Navigate to dashboard.php
   - [ ] ✅ Sidebar should BE EXPANDED
   - [ ] Navigate to chat.php
   - [ ] ✅ Sidebar should REMAIN EXPANDED

3. **Test Scenario 3:**
   - [ ] Collapse sidebar on any page
   - [ ] Close browser tab
   - [ ] Reopen http://localhost:8000/dashboard.php
   - [ ] ✅ Sidebar should BE COLLAPSED (state persisted)

#### LocalStorage Inspection:
- [ ] Open Browser DevTools (F12)
- [ ] Go to Application → Local Storage → http://localhost:8000
- [ ] Verify key: `sidebarCollapsed`
- [ ] Value should be `"true"` when collapsed, `"false"` when expanded

---

### **5. Responsive Design Tests**

#### Desktop (1920x1080):
- [ ] Sidebar displays properly at full width
- [ ] Toggle works smoothly
- [ ] Content doesn't break

#### Tablet (768x1024):
- [ ] Sidebar is visible
- [ ] Toggle functionality works
- [ ] Layout adapts properly

#### Mobile (375x667):
- [ ] Check if sidebar overlays or remains visible
- [ ] Toggle functionality works
- [ ] Navigation is accessible

---

### **6. Navigation Link Tests**

#### From Dashboard (Expanded Sidebar):
- [ ] Click each link - all pages load correctly
- [ ] Active state updates correctly on each page

#### From Dashboard (Collapsed Sidebar):
- [ ] Click each icon - all pages load correctly
- [ ] Collapsed state persists after navigation

---

### **7. Edge Cases**

#### Rapid Toggle:
- [ ] Click logo rapidly 5-10 times
- [ ] No visual glitches or broken animations
- [ ] State remains consistent

#### Multiple Tabs:
- [ ] Open dashboard in 2 tabs
- [ ] Collapse sidebar in Tab 1
- [ ] Refresh Tab 2
- [ ] ✅ Tab 2 should show collapsed sidebar

#### Browser Compatibility:
- [ ] Test in Chrome
- [ ] Test in Firefox
- [ ] Test in Edge
- [ ] All browsers work correctly

---

## 🐛 Bug Report Template

If you find issues, document them here:

### Bug #1:
- **Page:** _______________
- **Issue:** _______________
- **Steps to Reproduce:** _______________
- **Expected Behavior:** _______________
- **Actual Behavior:** _______________
- **Screenshot:** _______________

---

## ✨ Test Results Summary

**Total Tests:** 100+  
**Passed:** ___  
**Failed:** ___  
**Blocked:** ___  

**Overall Status:** ⬜ PASS | ⬜ FAIL | ⬜ NEEDS REVIEW

---

## 📝 Notes

- Server running at: http://localhost:8000
- Database: MySQL port 3307
- Test browser: _______________
- Tester: _______________

---

## 🎯 Critical Features to Verify

1. ✅ Logo click toggles sidebar
2. ✅ Icon-only mode when collapsed
3. ✅ Tooltips appear on hover
4. ✅ LocalStorage persists state across pages
5. ✅ Smooth 0.3s transitions
6. ✅ "Back to Dashboard" button removed from meditation.php
7. ✅ Sidebar present on all three pages
8. ✅ Active menu item highlights correctly

---

**Happy Testing! 🚀**
