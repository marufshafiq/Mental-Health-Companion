# 🎯 Quick Toggle Test Guide

## Server Status
✅ PHP Server Running: http://localhost:8000

## Pages to Test
1. **Dashboard:** http://localhost:8000/dashboard.php
2. **Chat:** http://localhost:8000/views/chat.php
3. **Meditation:** http://localhost:8000/views/meditation.php

---

## 🚀 Quick 5-Minute Test

### Step 1: Test Basic Toggle (Dashboard)
1. Open dashboard.php
2. Look at the left sidebar - you should see 🧠 logo + "Mental Health Companion"
3. **Click the 🧠 brain logo**
4. Watch the sidebar collapse to show only icons
5. **Click the logo again**
6. Watch it expand back

**✅ Expected:** Smooth animation, no glitches

---

### Step 2: Test Tooltips (Dashboard - Collapsed)
1. Collapse the sidebar (click logo)
2. **Hover over each icon:**
   - 📊 → Should show "Dashboard"
   - 📓 → Should show "Journal"
   - 😊 → Should show "Mood Tracker"
   - 💬 → Should show "AI Chatbot"
   - 🧘‍♀️ → Should show "Meditation & Resources"
   - 👤 → Should show "Profile"
   - 🚪 → Should show "Logout"

**✅ Expected:** Tooltip appears on the right of each icon

---

### Step 3: Test State Persistence
1. On dashboard, collapse the sidebar
2. Click "AI Chatbot" (💬 icon)
3. **Chat page loads - sidebar should STILL BE COLLAPSED**
4. Click "Meditation & Resources" (🧘‍♀️ icon)
5. **Meditation page loads - sidebar should STILL BE COLLAPSED**
6. Expand the sidebar (click logo)
7. Click "Dashboard" (📊 icon or text)
8. **Dashboard loads - sidebar should BE EXPANDED**

**✅ Expected:** Sidebar state persists across all pages

---

### Step 4: Test Meditation Page (Critical)
1. Go to meditation.php
2. **Verify:** "Back to Dashboard" button is GONE ❌
3. **Verify:** Sidebar is present on the left ✅
4. **Verify:** Purple gradient tip card displays beautifully ✅
5. **Verify:** Resources section looks good ✅
6. **Click logo** to collapse/expand
7. **Verify:** Page layout adjusts smoothly ✅

**✅ Expected:** Professional layout with sidebar, no back button

---

### Step 5: Test Page Refresh
1. Collapse sidebar on any page
2. Press F5 (refresh)
3. **Sidebar should remain collapsed**
4. Expand sidebar
5. Press F5 (refresh)
6. **Sidebar should remain expanded**

**✅ Expected:** State survives page reload

---

## 🔍 Visual Inspection Checklist

### When Expanded (250px):
- [ ] Logo visible
- [ ] Title "Mental Health Companion" visible
- [ ] All icons visible
- [ ] All text visible
- [ ] Active page highlighted (colored background)

### When Collapsed (80px):
- [ ] Logo visible (centered)
- [ ] Icons visible (centered)
- [ ] Text HIDDEN
- [ ] Title HIDDEN
- [ ] Tooltips appear on hover
- [ ] Main content has 80px left margin

---

## 🎨 Animation Quality Check

Watch for these during toggle:
- [ ] Smooth width transition (not instant)
- [ ] Text fades out/in smoothly
- [ ] Main content slides smoothly
- [ ] No jumping or glitching
- [ ] Duration feels right (~0.3 seconds)

---

## 🐛 Common Issues to Watch For

1. **Text doesn't hide when collapsed**
   - Issue: CSS not applied correctly

2. **Tooltips don't appear**
   - Issue: Missing data-tooltip attributes

3. **State doesn't persist**
   - Issue: localStorage not working
   - Check: F12 → Application → Local Storage

4. **Layout breaks on meditation page**
   - Issue: CSS conflicts with tip card

5. **Icons misaligned when collapsed**
   - Issue: Padding/centering issue

---

## ✅ Success Criteria

If all these work, the feature is PERFECT:
1. ✅ Logo click toggles sidebar
2. ✅ Smooth animations
3. ✅ Tooltips work
4. ✅ State persists across pages
5. ✅ State persists after refresh
6. ✅ Meditation page has sidebar (no back button)
7. ✅ All three pages consistent
8. ✅ No layout breaks

---

## 📸 Screenshot Checklist

Take screenshots of:
1. Dashboard - Expanded state
2. Dashboard - Collapsed state
3. Dashboard - Tooltip visible
4. Chat - Collapsed state
5. Meditation - Expanded state (showing purple tip)
6. Meditation - Collapsed state

---

## 🎉 Test Complete!

Once you've verified all the above, the toggle feature is production-ready!

**Questions or Issues?** Check the full test checklist in `TOGGLE_TEST_CHECKLIST.md`

---

**Server:** http://localhost:8000  
**Test Date:** October 11, 2025  
**Feature Version:** 1.0 - ChatGPT-style Collapsible Sidebar
